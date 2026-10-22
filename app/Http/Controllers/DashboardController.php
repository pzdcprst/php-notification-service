<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('dashboard', [
            'upcomingEvents' => $user->events()
                ->with('contact')
                ->where('starts_at', '>=', now())
                ->orderBy('starts_at')
                ->limit(6)
                ->get(),
            'pendingReminders' => $user->reminders()
                ->with('event')
                ->where('status', 'pending')
                ->orderBy('remind_at')
                ->limit(6)
                ->get(),
            'contactsCount' => $user->contacts()->count(),
            'eventsCount' => $user->events()->where('starts_at', '>=', now())->count(),
            'importantDatesCount' => $user->importantDates()->count(),
        ]);
    }
}
