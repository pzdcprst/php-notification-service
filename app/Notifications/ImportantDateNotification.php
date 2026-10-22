<?php

namespace App\Notifications;

use App\Models\ImportantDate;
use Illuminate\Notifications\Notification;

class ImportantDateNotification extends Notification
{
    public function __construct(public ImportantDate $importantDate) {}

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
            'important_date_id' => $this->importantDate->id,
            'title' => $this->importantDate->title,
            'date' => $this->importantDate->date->toDateString(),
            'contact' => $this->importantDate->contact->name,
        ];
    }
}
