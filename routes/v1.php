<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

/**
 * API Version 1 Routes
 * Prefix: /api/v1
 */

/**
 * Authentication Routes (Public)
 */
Route::middleware('throttle:login')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('v1.login');
});

Route::middleware('throttle:register')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('v1.register');
});

/**
 * Protected Routes (Require Authentication)
 */
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {

    // Auth Routes
    Route::post('/logout', [AuthController::class, 'logout'])->name('v1.logout');
    Route::get('/me', [AuthController::class, 'me'])->name('v1.me');

    // User Routes
    Route::apiResource('user', UserController::class)->names([
        'index' => 'v1.user.index',
        'store' => 'v1.user.store',
        'show' => 'v1.user.show',
        'update' => 'v1.user.update',
        'destroy' => 'v1.user.destroy',
    ]);
    Route::get('/user/all/paginated', [UserController::class, 'getAllPaginated'])->name('v1.user.paginated');

    // Sensitive operations (dengan rate limit lebih ketat)
    Route::middleware('throttle:sensitive')->group(function () {
        Route::put('/user/{id}/update-password', [UserController::class, 'updatePassword'])->name('v1.user.password');
        Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('v1.user.delete');
    });
});
