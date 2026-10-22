<?php

namespace App\Jobs;

use App\Models\Reminder;
use App\Notifications\ReminderNotification;
use App\Services\TelegramBot;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Throwable;

class SendReminderJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [60, 300];

    public int $timeout = 15;

    public int $uniqueFor = 3600;

    public function __construct(public int $reminderId) {}

    public function uniqueId(): string
    {
        return (string) $this->reminderId;
    }

    public function handle(TelegramBot $telegramBot): void
    {
        $claimedReminderId = DB::transaction(function (): ?int {
            $reminder = Reminder::query()
                ->whereKey($this->reminderId)
                ->where('status', Reminder::STATUS_PENDING)
                ->lockForUpdate()
                ->first();

            if ($reminder === null) {
                return null;
            }

            $reminder->forceFill(['status' => Reminder::STATUS_SENDING])->save();

            return $reminder->id;
        });

        if ($claimedReminderId === null) {
            return;
        }

        $reminder = Reminder::query()->with(['user', 'event.contact'])->findOrFail($claimedReminderId);

        try {
            if ($reminder->channel === Reminder::CHANNEL_DATABASE) {
                $reminder->user->notify(new ReminderNotification($reminder));
            } elseif (! $reminder->user->hasTelegram()) {
                $reminder->forceFill(['status' => Reminder::STATUS_FAILED])->save();

                return;
            } else {
                $event = $reminder->event;
                $message = "Напоминание: {$event->title}\n".
                    'Событие: '.$event->starts_at->format('d.m.Y H:i');

                if ($event->contact !== null) {
                    $message .= "\nКонтакт: {$event->contact->name}";
                }

                if ($event->description !== null && $event->description !== '') {
                    $message .= "\n\n{$event->description}";
                }

                $telegramBot->sendMessage($reminder->user->telegram_chat_id, $message);
            }

            Reminder::query()->whereKey($reminder->id)->where('status', Reminder::STATUS_SENDING)->update([
                'status' => Reminder::STATUS_SENT,
                'sent_at' => now(),
            ]);
        } catch (Throwable $exception) {
            Reminder::query()->whereKey($reminder->id)->where('status', Reminder::STATUS_SENDING)->update([
                'status' => Reminder::STATUS_PENDING,
            ]);

            throw $exception;
        }
    }

    public function failed(?Throwable $exception): void
    {
        Reminder::query()
            ->whereKey($this->reminderId)
            ->where('status', Reminder::STATUS_PENDING)
            ->update(['status' => Reminder::STATUS_FAILED]);
    }
}
