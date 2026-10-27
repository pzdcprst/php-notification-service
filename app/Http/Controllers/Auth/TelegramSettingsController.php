<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Telegram\CreateTelegramLinkAction;
use App\Http\Controllers\Controller;
use App\Services\TelegramBot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TelegramSettingsController extends Controller
{
    public function __construct(
        private readonly CreateTelegramLinkAction $createTelegramLinkAction,
    ) {}

    public function show(Request $request): View
    {
        return view('settings.telegram', ['user' => $request->user()]);
    }

    public function createLink(Request $request): RedirectResponse
    {
        $link = $this->createTelegramLinkAction->execute($request->user());

        return to_route('settings.telegram')->with('telegramLink', $link);
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
