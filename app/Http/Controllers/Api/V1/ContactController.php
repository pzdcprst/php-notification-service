<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContactController extends Controller
{

    /** 
     * TODO: реализовать поиск контактов (и других крудов) через сервис (query-параметром)
     * Реализовано: на теккущей странице на стороне пользователя
     */

    public function index(Request $request): AnonymousResourceCollection
    {
        return ContactResource::collection(
            $request->user()->contacts()->orderByDesc('id')->paginate(20)
        );
    }

    public function store(StoreContactRequest $request): JsonResponse
    {
        $contact = $request->user()->contacts()->create($request->validated());

        return ContactResource::make($contact)->response()->setStatusCode(201);
    }

    public function show(Request $request, Contact $contact): ContactResource
    {
        return ContactResource::make(
            $request->user()->contacts()->findOrFail($contact->getKey())
        );
    }

    public function update(UpdateContactRequest $request, Contact $contact): ContactResource
    {
        $contact = $request->user()->contacts()->findOrFail($contact->getKey());
        $contact->update($request->validated());

        return ContactResource::make($contact->refresh());
    }

    public function destroy(Request $request, Contact $contact): JsonResponse
    {
        $request->user()->contacts()->findOrFail($contact->getKey())->delete();

        return response()->json(status: 204);
    }
}
