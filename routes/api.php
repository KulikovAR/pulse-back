<?php

use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\TelegramController;
use App\Http\Controllers\AssetsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\ClientController;
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

    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('clients.index');
        Route::post('/', [ClientController::class, 'store'])->name('clients.store');
        Route::get('/{client}', [ClientController::class, 'show'])->name('clients.show');
        Route::put('/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    });

    Route::prefix('services')->group(function () {
        Route::get('/', [ServiceController::class, 'index'])->name('services.index');
        Route::post('/', [ServiceController::class, 'store'])->name('services.store');
        Route::get('/{service}', [ServiceController::class, 'show'])->name('services.show');
        Route::put('/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::delete('/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
    });

    Route::prefix('companies')->group(function () {
        Route::get('/', [CompanyController::class, 'index'])->name('companies.index');
        Route::post('/', [CompanyController::class, 'store'])->name('companies.store');
        Route::get('/client', [CompanyController::class, 'getByClientId'])->name('companies.by-client');
        Route::get('/{company}', [CompanyController::class, 'show'])->name('companies.show');
        Route::put('/{company}', [CompanyController::class, 'update'])->name('companies.update');
        Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');
    });
});

Route::get('/assets/{locale?}', [AssetsController::class, 'show'])->name('assets.index');

Route::prefix('telegram')->group(function () {
    Route::get('/client/{chat_id}', [TelegramController::class, 'getClientByChatId']);
    Route::post('/login', [TelegramController::class, 'login'])->name('telegram.login');
    Route::post('/admin/login', [TelegramController::class, 'adminLogin'])->name('telegram.admin.login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('events', [EventController::class, 'getEventsByClientId']);
    Route::get('events/company', [EventController::class, 'getEventsByCompanyId']);

    Route::prefix('event')->group(function () {
        Route::get('/{id}', [EventController::class, 'getEventById']);
        Route::post('', [EventController::class, 'createEvent']);
        Route::put('/{id}', [EventController::class, 'updateEvent']);
        Route::delete('/{id}', [EventController::class, 'deleteEvent']);
    });
});
