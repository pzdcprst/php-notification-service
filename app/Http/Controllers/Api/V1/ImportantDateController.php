<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImportantDateRequest;
use App\Http\Requests\UpdateImportantDateRequest;
use App\Http\Resources\ImportantDateResource;
use App\Models\ImportantDate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ImportantDateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return ImportantDateResource::collection(
            $request->user()->importantDates()->with('contact')->orderBy('date')->orderBy('id')->paginate(20)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreImportantDateRequest $request): JsonResponse
    {
        $importantDate = $request->user()->importantDates()->create($request->validated());

        return ImportantDateResource::make($importantDate->load('contact'))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, ImportantDate $importantDate): ImportantDateResource
    {
        return ImportantDateResource::make(
            $request->user()->importantDates()->with('contact')->findOrFail($importantDate->getKey())
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateImportantDateRequest $request, ImportantDate $importantDate): ImportantDateResource
    {
        $importantDate = $request->user()->importantDates()->findOrFail($importantDate->getKey());
        $importantDate->update($request->validated());

        return ImportantDateResource::make($importantDate->refresh()->load('contact'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ImportantDate $importantDate): JsonResponse
    {
        $request->user()->importantDates()->findOrFail($importantDate->getKey())->delete();

        return response()->json(status: 204);
    }
}
