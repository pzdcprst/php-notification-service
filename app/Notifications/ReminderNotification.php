<?php

namespace App\Notifications;

use App\Models\Reminder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReminderNotification extends Notification
{
    use Queueable;

    public function __construct(public Reminder $reminder) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'reminder_id' => $this->reminder->id,
            'title' => $this->reminder->event->title,
            'starts_at' => $this->reminder->event->starts_at->toIso8601String(),
            'contact' => $this->reminder->event->contact?->name,
        ];
    }
}
