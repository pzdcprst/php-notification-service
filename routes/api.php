<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\ImportantDateController;
use App\Http\Controllers\Api\V1\ReminderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->name('api.v1.')->middleware('auth:sanctum')->group(function (): void {
    Route::apiResource('contacts', ContactController::class);
    Route::apiResource('events', EventController::class);
    Route::apiResource('reminders', ReminderController::class);
    Route::apiResource('important-dates', ImportantDateController::class);
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

Route::prefix('v1')->name('api.v1.')->middleware('throttle:10,1')->group(function (): void {
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
});
