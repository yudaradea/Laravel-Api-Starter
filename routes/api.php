<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/**
 * Authentication Routes (Public)
 */
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

/**
 * Protected Routes (Require Authentication)
 */
Route::middleware('auth:sanctum')->group(function () {

    // Auth Routes
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/me', [AuthController::class, 'me'])->name('me');

    // User Routes
    Route::apiResource('user', UserController::class);
    Route::get('/user/all/paginated', [UserController::class, 'getAllPaginated']);
    Route::put('/user/{id}/update-password', [UserController::class, 'updatePassword']);

    // Add your other protected routes here...
});
