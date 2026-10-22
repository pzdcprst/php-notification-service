<?php

namespace App\Http\Controllers;

use App\Jobs\SendTelegramMessageJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class TelegramWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $secret = config('services.telegram.webhook_secret');
        $providedSecret = $request->header('X-Telegram-Bot-Api-Secret-Token');

        abort_unless(
            is_string($secret) && $secret !== '' && is_string($providedSecret) && hash_equals($secret, $providedSecret),
            HttpResponse::HTTP_FORBIDDEN,
        );

        $message = $request->input('message');

        if (! is_array($message) || data_get($message, 'chat.type') !== 'private') {
            return response()->noContent();
        }

        $text = (string) data_get($message, 'text', '');

        if (! preg_match('/^\/start(?:@\w+)?(?:\s+([A-Za-z0-9_-]+))?$/', trim($text), $matches)) {
            return response()->noContent();
        }

        $plainToken = $matches[1] ?? null;
        $chatId = (string) data_get($message, 'chat.id', '');

        if (! is_string($plainToken) || $plainToken === '' || $chatId === '') {
            return response()->noContent();
        }

        $tokenHash = hash('sha256', $plainToken);

        if (User::query()->where('telegram_chat_id', $chatId)->exists()) {
            return response()->noContent();
        }

        $linked = User::query()
            ->where('telegram_link_token', $tokenHash)
            ->where('telegram_link_expires_at', '>', now())
            ->update([
                'telegram_chat_id' => $chatId,
                'telegram_link_token' => null,
                'telegram_link_expires_at' => null,
                'updated_at' => now(),
            ]);

        if ($linked === 1) {
            SendTelegramMessageJob::dispatch($chatId, 'Telegram успешно подключен. Теперь сюда будут приходить напоминания.');
        }

        return response()->noContent();
    }
}
