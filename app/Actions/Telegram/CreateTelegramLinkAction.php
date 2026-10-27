<?php

namespace App\Actions\Telegram;

use App\Models\User;
use RuntimeException;
use Illuminate\Support\Str;

class CreateTelegramLinkAction
{
    public function execute(User $user): string
    {
        $botUsername = config('services.telegram.bot_username');

        if (! is_string($botUsername) || $botUsername === '') {
            throw new RuntimeException('The Telegram bot username is not configured.');
        }

        $plainToken = Str::random(48);

        $user->issueTelegramLinkToken($plainToken);

        return $this->buildLink($botUsername, $plainToken);
    }

    private function buildLink(string $botUsername, string $plainToken) : string
    {
        return "https://t.me/{$botUsername}?start={$plainToken}";
    }
}