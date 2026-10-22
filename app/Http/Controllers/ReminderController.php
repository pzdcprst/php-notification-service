<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReminderRequest;
use App\Http\Requests\UpdateReminderRequest;
use App\Models\Reminder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReminderController extends Controller
{
    public function index(Request $request): View
    {
        return view('reminders.index', [
            'reminders' => $request->user()->reminders()->with('event')->orderByDesc('remind_at')->paginate(20),
            'events' => $request->user()->events()->orderBy('starts_at')->get(['id', 'title', 'starts_at']),
        ]);
    }

    public function store(StoreReminderRequest $request): RedirectResponse
    {
        $request->user()->reminders()->create($request->validated());

        return to_route('reminders.index')->with('status', 'Напоминание добавлено.');
    }

    public function update(UpdateReminderRequest $request, Reminder $reminder): RedirectResponse
    {
        $reminder = $request->user()->reminders()->findOrFail($reminder->getKey());
        $reminder->update($request->validated());
        $reminder->forceFill(['status' => Reminder::STATUS_PENDING, 'sent_at' => null])->save();

        return to_route('reminders.index')->with('status', 'Напоминание обновлено.');
    }

    public function destroy(Request $request, Reminder $reminder): RedirectResponse
    {
        $request->user()->reminders()->findOrFail($reminder->getKey())->delete();

        return to_route('reminders.index')->with('status', 'Напоминание удалено.');
    }
}
