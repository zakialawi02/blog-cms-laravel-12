<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\WelcomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Welcome Route
|--------------------------------------------------------------------------
*/
Route::get('/', WelcomeController::class)->name('api.welcome');

/*
|--------------------------------------------------------------------------
| API Routes (v1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->as('api.')->group(function () {

    /*
    |----------------------------------------------------------------------
    | API v1 Welcome
    |----------------------------------------------------------------------
    */
    Route::get('/', WelcomeController::class)->name('v1.welcome');

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

    /*
    |----------------------------------------------------------------------
    | Menus
    |----------------------------------------------------------------------
    */
    // Public — list and show
    Route::apiResource('menus', MenuController::class)->only(['index', 'show']);
    Route::get('menus/location/{location}', [MenuController::class, 'showByLocation'])->name('menus.location');

    // Protected — store, update, destroy, and sync items (requires: ability:menu.manage)
    Route::middleware(['auth:sanctum', 'ability:menu.manage'])->group(function () {
        Route::apiResource('menus', MenuController::class)->except(['index', 'show']);
        Route::post('menus/{menu}/items', [MenuController::class, 'syncItems'])->name('menus.syncItems');
    });

    /*
    |----------------------------------------------------------------------
    | Public Articles
    |----------------------------------------------------------------------
    */
    Route::prefix('articles')->name('articles.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\PublicArticleController::class, 'index'])->name('index');
        Route::get('/summary', [\App\Http\Controllers\Api\PublicArticleController::class, 'summary'])->name('summary');
        Route::get('/popular', [\App\Http\Controllers\Api\PublicArticleController::class, 'popularPost'])->name('popular');
        Route::get('/category/{slug?}', [\App\Http\Controllers\Api\PublicArticleController::class, 'articlesByCategory'])->name('category');
        Route::get('/tag/{slug?}', [\App\Http\Controllers\Api\PublicArticleController::class, 'articlesByTag'])->name('tag');
        Route::get('/user/{username?}', [\App\Http\Controllers\Api\PublicArticleController::class, 'articlesByUser'])->name('user');
        Route::get('/archive/{year?}', [\App\Http\Controllers\Api\PublicArticleController::class, 'articlesByYear'])->name('year');
        Route::get('/archive/{year}/{month?}', [\App\Http\Controllers\Api\PublicArticleController::class, 'articlesByMonth'])->name('month');
        Route::get('/{slug}', [\App\Http\Controllers\Api\PublicArticleController::class, 'show'])->name('show');
    });

});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/api_auth.php';
