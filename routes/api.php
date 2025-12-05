<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->as('api.')->group(function () {
    Route::get('/docs.json', [App\Http\Controllers\Api\ApiDocsController::class, 'schema'])->name('docs.schema');

    Route::apiResource('articles', App\Http\Controllers\Api\ArticleController::class)
        ->parameters(['articles' => 'article:slug'])
        ->only(['index', 'show']);

    Route::apiResource('categories', App\Http\Controllers\Api\CategoryController::class)
        ->parameters(['categories' => 'category:slug'])
        ->only(['index', 'show']);

    Route::apiResource('tags', App\Http\Controllers\Api\TagController::class)
        ->parameters(['tags' => 'tag:slug'])
        ->only(['index', 'show']);

    Route::apiResource('pages', App\Http\Controllers\Api\PageController::class)
        ->parameters(['pages' => 'page:slug'])
        ->only(['index', 'show']);

    Route::get('/menus', App\Http\Controllers\Api\MenuController::class . '@index')->name('menus.index');
    Route::get('/settings', App\Http\Controllers\Api\WebSettingController::class)->name('settings');
    Route::get('/newsletter', [App\Http\Controllers\Api\NewsletterController::class, 'index'])->middleware('auth:sanctum')->name('newsletter.index');
    Route::post('/newsletter', [App\Http\Controllers\Api\NewsletterController::class, 'store'])->name('newsletter.store');
    Route::delete('/newsletter/{newsletter}', [App\Http\Controllers\Api\NewsletterController::class, 'destroy'])->middleware('auth:sanctum')->name('newsletter.destroy');

    Route::get('/articles/{article:slug}/comments', [App\Http\Controllers\Api\CommentController::class, 'index'])->name('articles.comments.index');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::apiResource('articles', App\Http\Controllers\Api\ArticleController::class)
            ->parameters(['articles' => 'article:slug'])
            ->only(['store', 'update', 'destroy']);
        Route::apiResource('categories', App\Http\Controllers\Api\CategoryController::class)
            ->parameters(['categories' => 'category:slug'])
            ->only(['store', 'update', 'destroy']);
        Route::apiResource('tags', App\Http\Controllers\Api\TagController::class)
            ->parameters(['tags' => 'tag:slug'])
            ->only(['store', 'update', 'destroy']);
        Route::apiResource('pages', App\Http\Controllers\Api\PageController::class)
            ->parameters(['pages' => 'page:slug'])
            ->only(['store', 'update', 'destroy']);
        Route::post('/articles/{article:slug}/comments', [App\Http\Controllers\Api\CommentController::class, 'store'])->name('articles.comments.store');
        Route::delete('/comments/{comment}', [App\Http\Controllers\Api\CommentController::class, 'destroy'])->name('articles.comments.destroy');
        Route::apiResource('users', App\Http\Controllers\Api\UserController::class);
        Route::get('/user', [App\Http\Controllers\Api\UserController::class, 'me'])->name('user.me');
        Route::patch('/user', [App\Http\Controllers\Api\UserController::class, 'updateMyProfile'])->name('user.me.update');
        Route::patch('/user/photo', [App\Http\Controllers\Api\UserController::class, 'updateMyPhotoProfile'])->name('user.photo');
        Route::delete('/user/destroy/me', [App\Http\Controllers\Api\UserController::class, 'destroyMyAccount'])->name('user.destroy.me');
    });

    Route::middleware(['auth:sanctum'])->group(function () {
        //
    });
});


// Auth routes for API
require __DIR__ . '/api_auth.php';
