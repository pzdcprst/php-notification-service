<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReminderRequest;
use App\Http\Requests\UpdateReminderRequest;
use App\Http\Resources\ReminderResource;
use App\Models\Reminder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReminderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return ReminderResource::collection(
            $request->user()->reminders()->with('event')->orderBy('remind_at')->orderBy('id')->paginate(20)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReminderRequest $request): JsonResponse
    {
        $reminder = $request->user()->reminders()->create($request->validated());

        return ReminderResource::make($reminder->load('event'))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Reminder $reminder): ReminderResource
    {
        return ReminderResource::make(
            $request->user()->reminders()->with('event')->findOrFail($reminder->getKey())
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReminderRequest $request, Reminder $reminder): ReminderResource
    {
        $reminder = $request->user()->reminders()->findOrFail($reminder->getKey());
        $reminder->update($request->validated());

        return ReminderResource::make($reminder->refresh()->load('event'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Reminder $reminder): JsonResponse
    {
        $request->user()->reminders()->findOrFail($reminder->getKey())->delete();

        return response()->json(status: 204);
    }
}
