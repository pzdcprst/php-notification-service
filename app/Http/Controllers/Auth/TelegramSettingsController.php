<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TelegramSettingsController extends Controller
{
    public function show(Request $request): View
    {
        return view('settings.telegram', ['user' => $request->user()]);
    }

    public function createLink(Request $request): RedirectResponse
    {
        $botUsername = config('services.telegram.bot_username');

        if (! is_string($botUsername) || $botUsername === '') {
            return to_route('settings.telegram')->withErrors([
                'telegram' => 'Бот еще не настроен. Укажите TELEGRAM_BOT_USERNAME.',
            ]);
        }

        $plainToken = Str::random(48);

        $request->user()->forceFill([
            'telegram_link_token' => hash('sha256', $plainToken),
            'telegram_link_expires_at' => now()->addMinutes(15),
        ])->save();

        return to_route('settings.telegram')->with('telegramLink', "https://t.me/{$botUsername}?start={$plainToken}");
    }

    public function disconnect(Request $request): RedirectResponse
    {
        $request->user()->forceFill([
            'telegram_chat_id' => null,
            'telegram_link_token' => null,
            'telegram_link_expires_at' => null,
        ])->save();

        return to_route('settings.telegram')->with('status', 'Telegram отключен.');
    }
}
