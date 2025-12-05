<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->as('api.')->group(function () {
    Route::get('articles', [App\Http\Controllers\Api\ArticleController::class, 'index']);
    Route::get('articles/{article}', [App\Http\Controllers\Api\ArticleController::class, 'show']);
    Route::get('categories', [App\Http\Controllers\Api\CategoryController::class, 'index']);
    Route::get('categories/{category}', [App\Http\Controllers\Api\CategoryController::class, 'show']);
    Route::get('tags', [App\Http\Controllers\Api\TagController::class, 'index']);
    Route::get('tags/{tag}', [App\Http\Controllers\Api\TagController::class, 'show']);
    Route::get('comments', [App\Http\Controllers\Api\CommentController::class, 'index']);
    Route::get('comments/{comment}', [App\Http\Controllers\Api\CommentController::class, 'show']);
    Route::get('pages', [App\Http\Controllers\Api\PageController::class, 'index']);
    Route::get('pages/{page}', [App\Http\Controllers\Api\PageController::class, 'show']);
    Route::get('menus', [App\Http\Controllers\Api\MenuController::class, 'index']);
    Route::get('menus/{menu}', [App\Http\Controllers\Api\MenuController::class, 'show']);
    Route::get('newsletter', [App\Http\Controllers\Api\NewsletterController::class, 'index']);
    Route::get('request-contributors', [App\Http\Controllers\Api\RequestContributorController::class, 'index']);
    Route::get('request-contributors/{request_contributor}', [App\Http\Controllers\Api\RequestContributorController::class, 'show']);
    Route::get('settings', [App\Http\Controllers\Api\WebSettingController::class, 'index']);
    Route::get('settings/{setting}', [App\Http\Controllers\Api\WebSettingController::class, 'show']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::apiResource('users', App\Http\Controllers\Api\UserController::class);
        Route::get('/user', [App\Http\Controllers\Api\UserController::class, 'me'])->name('user.me');
        Route::patch('/user', [App\Http\Controllers\Api\UserController::class, 'updateMyProfile'])->name('user.me.update');
        Route::patch('/user/photo', [App\Http\Controllers\Api\UserController::class, 'updateMyPhotoProfile'])->name('user.photo');
        Route::delete('/user/destroy/me', [App\Http\Controllers\Api\UserController::class, 'destroyMyAccount'])->name('user.destroy.me');

        Route::apiResource('articles', App\Http\Controllers\Api\ArticleController::class)->except(['index', 'show']);
        Route::apiResource('categories', App\Http\Controllers\Api\CategoryController::class)->except(['index', 'show']);
        Route::apiResource('tags', App\Http\Controllers\Api\TagController::class)->except(['index', 'show']);
        Route::apiResource('comments', App\Http\Controllers\Api\CommentController::class)->except(['index', 'show']);
        Route::apiResource('pages', App\Http\Controllers\Api\PageController::class)->except(['index', 'show']);
        Route::apiResource('menus', App\Http\Controllers\Api\MenuController::class)->except(['index', 'show']);
        Route::apiResource('menu-items', App\Http\Controllers\Api\MenuItemController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('newsletter', App\Http\Controllers\Api\NewsletterController::class)->only(['store', 'destroy']);
        Route::apiResource('request-contributors', App\Http\Controllers\Api\RequestContributorController::class)->except(['index', 'show']);
        Route::apiResource('settings', App\Http\Controllers\Api\WebSettingController::class)->except(['index', 'show']);
    });
});


// Auth routes for API
require __DIR__ . '/api_auth.php';
