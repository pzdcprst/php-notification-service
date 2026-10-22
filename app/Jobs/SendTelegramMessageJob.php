<?php

namespace App\Jobs;

use App\Services\TelegramBot;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendTelegramMessageJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $chatId, public string $text) {}

    public function handle(TelegramBot $telegramBot): void
    {
        $telegramBot->sendMessage($this->chatId, $this->text);
    }
}
