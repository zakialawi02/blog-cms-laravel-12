<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->as('api.')->group(function () {
    // === Role-based: User Management (superadmin & admin only) ===
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::apiResource('users', App\Http\Controllers\Api\UserController::class);
        Route::post('/users/{id}/restore', [App\Http\Controllers\Api\UserController::class, 'restore'])->name('users.restore');
    });

    // === Authenticated User: My Profile ===
    Route::middleware(['auth:sanctum'])->prefix('user')->as('user.')->group(function () {
        Route::get('/me', [App\Http\Controllers\Api\UserController::class, 'me'])->name('me');
        Route::patch('/', [App\Http\Controllers\Api\UserController::class, 'updateMyProfile'])->name('me.update');
        Route::patch('/password', [App\Http\Controllers\Api\UserController::class, 'updateMyPassword'])->name('password');
        Route::patch('/photo', [App\Http\Controllers\Api\UserController::class, 'updateMyPhotoProfile'])->name('photo');
        Route::delete('/', [App\Http\Controllers\Api\UserController::class, 'destroyMyAccount'])->name('destroy.me');
    });

    // === Ability-based: Categories (requires 'write' ability) ===
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::apiResource('categories', App\Http\Controllers\Api\CategoryController::class);
    });
});


// Auth routes for API
require __DIR__ . '/api_auth.php';
