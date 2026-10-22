<?php

namespace App\Console\Commands;

use App\Jobs\SendReminderJob;
use App\Models\Reminder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('reminders:dispatch')]
#[Description('Dispatch due reminders to the queue')]
class DispatchDueReminders extends Command
{
    public function handle(): int
    {
        $dispatched = 0;

        Reminder::query()
            ->where('status', Reminder::STATUS_SENDING)
            ->where('updated_at', '<', now()->subMinutes(2))
            ->update(['status' => Reminder::STATUS_PENDING]);

        Reminder::query()
            ->where('status', Reminder::STATUS_PENDING)
            ->where('remind_at', '<=', now())
            ->orderBy('id')
            ->chunkById(100, function ($reminders) use (&$dispatched): void {
                foreach ($reminders as $reminder) {
                    SendReminderJob::dispatch($reminder->id)->afterCommit();
                    $dispatched++;
                }
            });

        $this->info("Dispatched {$dispatched} due reminder(s).");

        return self::SUCCESS;
    }
}
