<?php

use App\Http\Controllers\DocumentationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dokumentasi');
});

// Documentation Routes
Route::prefix('dokumentasi')->group(function () {
    Route::get('/', [DocumentationController::class, 'index'])->name('docs.index');
    Route::get('/{slug}', [DocumentationController::class, 'show'])->name('docs.show');
});
