<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(Request $request): View
    {
        return view('contacts.index', [
            'contacts' => $request->user()->contacts()->withCount(['events', 'importantDates'])->orderBy('name')->paginate(20),
        ]);
    }

    public function store(StoreContactRequest $request): RedirectResponse
    {
        $request->user()->contacts()->create($request->validated());

        return to_route('contacts.index')->with('status', 'Контакт добавлен.');
    }

    public function update(UpdateContactRequest $request, Contact $contact): RedirectResponse
    {
        $contact = $request->user()->contacts()->findOrFail($contact->getKey());
        $contact->update($request->validated());

        return to_route('contacts.index')->with('status', 'Контакт обновлен.');
    }

    public function destroy(Request $request, Contact $contact): RedirectResponse
    {
        $request->user()->contacts()->findOrFail($contact->getKey())->delete();

        return to_route('contacts.index')->with('status', 'Контакт удален.');
    }
}
