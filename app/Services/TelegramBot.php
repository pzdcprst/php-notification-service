<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class TelegramBot
{
    public function sendMessage(string $chatId, string $text): void
    {
        $response = $this->request('sendMessage', [
            'chat_id' => $chatId,
            'text' => $text,
        ]);

        if ($response->json('ok') !== true) {
            throw new RuntimeException('Telegram rejected the sendMessage request.');
        }
    }

    public function setWebhook(string $url, string $secret): void
    {
        $response = $this->request('setWebhook', [
            'url' => $url,
            'secret_token' => $secret,
            'allowed_updates' => ['message'],
        ]);

        if ($response->json('ok') !== true) {
            throw new RuntimeException('Telegram rejected the setWebhook request.');
        }
    }

    private function request(string $method, array $payload): Response
    {
        $token = config('services.telegram.bot_token');

        if (! is_string($token) || $token === '') {
            throw new RuntimeException('The Telegram bot token is not configured.');
        }

        try {
            return Http::connectTimeout(3)
                ->timeout(10)
                ->post("https://api.telegram.org/bot{$token}/{$method}", $payload)
                ->throw();
        } catch (ConnectionException $exception) {
            throw $exception;
        }
    }
}
