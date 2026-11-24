<?php

use App\Http\Controllers\Api\V1\ImageController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/images', [ImageController::class, 'index'])->name('images.index');
    Route::post('/images', [ImageController::class, 'store'])->name('images.store');
    Route::get('/images/{slug}', [ImageController::class, 'show'])->name('images.show');
    Route::match(['patch', 'put'], '/images/{slug}', [ImageController::class, 'update'])
        ->name('images.update');
    Route::delete('/images/{slug}', [ImageController::class, 'destroy'])->name('images.destroy');
});
