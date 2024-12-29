<?php

use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\TelegramController;
use App\Http\Controllers\AssetsController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('/verify', [AuthController::class, 'verify'])->name('auth.verify');

    Route::middleware('refresh')->group(function () {
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
});

Route::get('/assets/{locale?}', [AssetsController::class, 'show'])->name('assets.index');

// Route::prefix('')->group(function () {
//     Route::get('telegram/client/{chat_id}', [TelegramController::class, 'getClientByChatId']);
//     Route::get('events/{client_id}', [EventController::class, 'getEventsByClientId']);
//     Route::get('event/{id}', [EventController::class, 'getEventById']);
//     Route::post('event', [EventController::class, 'createEvent']);
//     Route::put('event/{id}', [EventController::class, 'updateEvent']);
//     Route::delete('event/{id}', [EventController::class, 'deleteEvent']);
// });

Route::prefix('telegram')->group(function () {
    Route::get('/client/{chat_id}', [TelegramController::class, 'getClientByChatId']);
    Route::post('/login', [TelegramController::class, 'login'])->name('telegram.login');
});

Route::get('events/{client_id}', [EventController::class, 'getEventsByClientId']);

Route::prefix('event')->group(function () {
    Route::get('/{id}', [EventController::class, 'getEventById']);
    Route::post('', [EventController::class, 'createEvent']);
    Route::put('/{id}', [EventController::class, 'updateEvent']);
    Route::delete('/{id}', [EventController::class, 'deleteEvent']);
});
