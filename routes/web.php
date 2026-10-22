<?php

use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\Auth\TelegramSettingsController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ImportantDateController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
    Route::get('/register', [RegistrationController::class, 'create'])->name('register');
    Route::post('/register', [RegistrationController::class, 'store'])->middleware('throttle:5,1')->name('register.store');
});

Route::post('/telegram/webhook', TelegramWebhookController::class)->name('telegram.webhook');

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('contacts', ContactController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('events', EventController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('important-dates', ImportantDateController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('reminders', ReminderController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::prefix('settings')->name('settings.')->group(function (): void {
        Route::get('/telegram', [TelegramSettingsController::class, 'show'])->name('telegram');
        Route::post('/telegram/link', [TelegramSettingsController::class, 'createLink'])->name('telegram.link');
        Route::delete('/telegram', [TelegramSettingsController::class, 'disconnect'])->name('telegram.disconnect');
    });
});
