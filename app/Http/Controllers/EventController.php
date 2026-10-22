<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(Request $request): View
    {
        return view('events.index', [
            'events' => $request->user()->events()->with('contact')->orderByDesc('starts_at')->paginate(20),
            'contacts' => $request->user()->contacts()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        $request->user()->events()->create($request->validated());

        return to_route('events.index')->with('status', 'Событие добавлено.');
    }

    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        $event = $request->user()->events()->findOrFail($event->getKey());
        $event->update($request->validated());

        return to_route('events.index')->with('status', 'Событие обновлено.');
    }

    public function destroy(Request $request, Event $event): RedirectResponse
    {
        $request->user()->events()->findOrFail($event->getKey())->delete();

        return to_route('events.index')->with('status', 'Событие удалено.');
    }
}
