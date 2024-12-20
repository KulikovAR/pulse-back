<?php

use App\Http\Controllers\Auth\AuthSessionController;
use App\Http\Controllers\PrivateStorageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
| Get csrf token if front on same domain:
| GET|HEAD  sanctum/csrf-cookie
|
*/
// Route::post('/login/session', [AuthSessionController::class, 'store'])->name('login.stateful');

// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/private/{filePath}', [PrivateStorageController::class, 'index'])->where(['filePath' => '.*'])->name('storage.view');
//     Route::delete('/logout/session', [AuthSessionController::class, 'destroy'])->name('logout.stateful');
// });

// Route::get('/storage/private/{filePath}', [PrivateStorageController::class, 'index'])->middleware(['signed', 'throttle:60,1'])->where(['filePath' => '.*'])->name('storage.private');
