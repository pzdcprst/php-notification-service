<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return EventResource::collection(
            $request->user()->events()->with('contact')->orderBy('starts_at')->orderBy('id')->paginate(20)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request): JsonResponse
    {
        $event = $request->user()->events()->create($request->validated());

        return EventResource::make($event->load('contact'))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Event $event): EventResource
    {
        return EventResource::make(
            $request->user()->events()->with('contact')->findOrFail($event->getKey())
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, Event $event): EventResource
    {
        $event = $request->user()->events()->findOrFail($event->getKey());
        $event->update($request->validated());

        return EventResource::make($event->refresh()->load('contact'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Event $event): JsonResponse
    {
        $request->user()->events()->findOrFail($event->getKey())->delete();

        return response()->json(status: 204);
    }
}
