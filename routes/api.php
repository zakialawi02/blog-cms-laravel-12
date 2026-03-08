<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (v1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->as('api.')->group(function () {

    /*
    |----------------------------------------------------------------------
    | User Management  (requires: auth + ability:user.manage)
    |----------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum', 'ability:user.manage'])->group(function () {
        Route::apiResource('users', UserController::class);
        Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    });

    /*
    |----------------------------------------------------------------------
    | Authenticated User Profile  (prefix: /user)
    |----------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum'])->prefix('user')->as('user.')->group(function () {
        Route::get('/me', [UserController::class, 'me'])->name('me');
        Route::patch('/', [UserController::class, 'updateMyProfile'])->name('me.update');
        Route::patch('/password', [UserController::class, 'updateMyPassword'])->name('password');
        Route::patch('/photo', [UserController::class, 'updateMyPhotoProfile'])->name('photo');
        Route::delete('/', [UserController::class, 'destroyMyAccount'])->name('destroy.me');
    });

    /*
    |----------------------------------------------------------------------
    | Categories
    |----------------------------------------------------------------------
    */
    // Public — list only
    Route::apiResource('categories', CategoryController::class)->only(['index']);

    // Protected — CRUD except index  (requires: ability:category.manage)
    Route::middleware(['auth:sanctum', 'ability:category.manage'])->group(function () {
        Route::apiResource('categories', CategoryController::class)->except(['index']);
    });

    /*
    |----------------------------------------------------------------------
    | Tags
    |----------------------------------------------------------------------
    */
    // Public — list only
    Route::apiResource('tags', TagController::class)->only(['index']);

    // Protected — store only  (requires: ability:tag.create)
    Route::middleware(['auth:sanctum', 'ability:tag.create'])->group(function () {
        Route::apiResource('tags', TagController::class)->only(['store']);
    });

    // Protected — show / update / destroy  (requires: ability:tag.manage)
    Route::middleware(['auth:sanctum', 'ability:tag.manage'])->group(function () {
        Route::apiResource('tags', TagController::class)->except(['index', 'store']);
    });
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/api_auth.php';
