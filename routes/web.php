<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public Gallery and Timeline
Route::get('/', [ImageController::class, 'index'])->name('images.index');

// Authentication routes (Sign In & Sign Out)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Authenticated user routes (Upload, Edit, Delete, Profile)
Route::middleware('auth')->group(function () {
    // User Profile & Password settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Photograph Management
    Route::get('/upload', [ImageController::class, 'create'])->name('images.create');
    Route::post('/images', [ImageController::class, 'store'])->name('images.store');
    Route::get('/{slug}/edit', [ImageController::class, 'edit'])
        ->where('slug', '^(?!upload|images|storage|build|_boost|up|login|logout|profile).*$')
        ->name('images.edit');
    Route::patch('/{slug}', [ImageController::class, 'update'])
        ->where('slug', '^(?!upload|images|storage|build|_boost|up|login|logout|profile).*$')
        ->name('images.update');
    Route::delete('/{slug}', [ImageController::class, 'destroy'])
        ->where('slug', '^(?!upload|images|storage|build|_boost|up|login|logout|profile).*$')
        ->name('images.destroy');
});

// Public Individual Viewing and Original Download
Route::get('/{slug}', [ImageController::class, 'show'])
    ->where('slug', '^(?!upload|images|storage|build|_boost|up|login|logout|profile).*$')
    ->name('images.show');

Route::get('/{slug}/download', [ImageController::class, 'download'])
    ->where('slug', '^(?!upload|images|storage|build|_boost|up|login|logout|profile).*$')
    ->name('images.download');
