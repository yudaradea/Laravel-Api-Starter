<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes V1
|--------------------------------------------------------------------------
|
| Prefix: /api/v1
|
*/

// --- Authentication Routes ---
Route::middleware('throttle:login')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::middleware('throttle:register')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

// --- Protected Routes ---
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/me', [AuthController::class, 'me'])->name('me');

    // Profile (Standardized)
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']); // Handles upload via FormRequest

    // User Management
    Route::apiResource('user', UserController::class);
    Route::get('/user/all/paginated', [UserController::class, 'getAllPaginated']);

    // Sensitive Ops
    Route::middleware('throttle:sensitive')->group(function () {
        Route::put('/user/{id}/update-password', [UserController::class, 'updatePassword']);
        Route::delete('/user/{id}', [UserController::class, 'destroy']);
    });
});
