<?php

namespace App\Console\Commands;

use App\Services\TelegramBot;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use RuntimeException;

#[Signature('telegram:set-webhook')]
#[Description('Register the Telegram bot webhook URL')]
class TelegramSetWebhook extends Command
{
    public function handle(TelegramBot $telegramBot): int
    {
        $url = route('telegram.webhook');
        $secret = config('services.telegram.webhook_secret');

        if (! str_starts_with($url, 'https://')) {
            throw new RuntimeException('Telegram webhooks require an HTTPS APP_URL.');
        }

        if (! is_string($secret) || strlen($secret) < 32) {
            throw new RuntimeException('TELEGRAM_WEBHOOK_SECRET must contain at least 32 characters.');
        }

        $telegramBot->setWebhook($url, $secret);
        $this->info("Telegram webhook registered: {$url}");

        return self::SUCCESS;
    }
}
