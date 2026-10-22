<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImportantDateRequest;
use App\Http\Requests\UpdateImportantDateRequest;
use App\Models\ImportantDate;

class ImportantDateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreImportantDateRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ImportantDate $importantDate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateImportantDateRequest $request, ImportantDate $importantDate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ImportantDate $importantDate)
    {
        //
    }
}
