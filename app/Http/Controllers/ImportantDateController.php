<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImportantDateRequest;
use App\Http\Requests\UpdateImportantDateRequest;
use App\Models\ImportantDate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImportantDateController extends Controller
{
    public function index(Request $request): View
    {
        return view('important-dates.index', [
            'importantDates' => $request->user()->importantDates()->with('contact')->orderBy('date')->paginate(20),
            'contacts' => $request->user()->contacts()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreImportantDateRequest $request): RedirectResponse
    {
        $request->user()->importantDates()->create($request->validated());

        return to_route('important-dates.index')->with('status', 'Важная дата добавлена.');
    }

    public function update(UpdateImportantDateRequest $request, ImportantDate $importantDate): RedirectResponse
    {
        $importantDate = $request->user()->importantDates()->findOrFail($importantDate->getKey());
        $importantDate->update($request->validated());

        return to_route('important-dates.index')->with('status', 'Важная дата обновлена.');
    }

    public function destroy(Request $request, ImportantDate $importantDate): RedirectResponse
    {
        $request->user()->importantDates()->findOrFail($importantDate->getKey())->delete();

        return to_route('important-dates.index')->with('status', 'Важная дата удалена.');
    }
}
