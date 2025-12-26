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

/**
 * Protected Routes (Require Authentication)
 */
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {

    // Auth Routes
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/me', [AuthController::class, 'me'])->name('me');

    // User Routes
    Route::apiResource('user', UserController::class);
    Route::get('/user/all/paginated', [UserController::class, 'getAllPaginated']);

    // Sensitive operations (dengan rate limit lebih ketat)
    Route::middleware('throttle:sensitive')->group(function () {
        Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password');
        Route::put('/user/{id}/update-password', [UserController::class, 'updatePassword']);
        Route::delete('/user/{id}', [UserController::class, 'destroy']);
    });

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);

    // Role & Permission Routes
    Route::get('/roles/capabilities', [\App\Http\Controllers\RoleController::class, 'capabilities'])->name('roles.capabilities');
    Route::apiResource('roles', \App\Http\Controllers\RoleController::class);
    Route::apiResource('permissions', \App\Http\Controllers\PermissionController::class);

    // Add your other protected routes here...
});
