<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AiController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\WebSettingController;
use App\Http\Controllers\ArticleViewController;

Route::get('/docs', function () {
    return view('pages.docs');
})->name('docs');

Route::prefix('dashboard')->name('admin.')->group(function () {
    Route::middleware(['auth', 'verified', 'role:superadmin'])->group(function () {
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/web', [WebSettingController::class, 'index'])->name('web.index');
            Route::put('/web', [WebSettingController::class, 'update'])->name('web.update');
            Route::get('/menus', [MenuController::class, 'index'])->name('menu.index');
            Route::post('/menus/create', [MenuController::class, 'createMenu'])->name('menu.create');
            Route::delete('/menu/{menu}', [MenuController::class, 'destroy'])->name('menu.delete');
            Route::get('/menus/list', [MenuController::class, 'getMenus'])->name('menu.list');
            Route::get('/menus/{menu}/items', [MenuController::class, 'getMenuItems'])->name('menu.items');
            Route::post('/menus/store-item', [MenuController::class, 'storeMenuItem'])->name('menu.storeItem');
            Route::post('/menus/update-structure', [MenuController::class, 'updateMenuStructure'])->name('menu.updateStructure');
            Route::delete('/menu/items/{item}', [MenuController::class, 'deleteItem'])->name('menu.deleteItem');
        });

        Route::resource('users', UserController::class)->except('create', 'edit');

        Route::get('/system-back-info', [DashboardController::class, 'info'])->name('info');

        // Categories
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::get('/categories/{category:slug}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category:slug}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category:slug}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Pages builder
        Route::patch('/pages/{id}/store-project', [PageController::class, 'storeProject'])->name('pages.storeproject');
        Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
        Route::post('/pages', [PageController::class, 'store'])->name('pages.store');
        Route::get('/pages/create', [PageController::class, 'create'])->name('pages.create');
        Route::get('/pages/{page:id}/edit', [PageController::class, 'edit'])->name('pages.edit');
        Route::get('/pages/{page:id}/builder', [PageController::class, 'builder'])->name('pages.builder');
        Route::put('/pages/{page:id}', [PageController::class, 'update'])->name('pages.update');
        Route::delete('/pages/{page:id}', [PageController::class, 'destroy'])->name('pages.destroy');
        Route::get('/pages/layout', [PageController::class, 'layout'])->name('pages.layout.index');
        Route::put('/pages/layout/update', [PageController::class, 'layoutUpdate'])->name('pages.layout.update');
    });

    Route::middleware(['auth', 'verified', 'role:superadmin,admin'])->group(function () {
        Route::resource('tags', TagController::class)->parameters(['tags' => 'tag:slug'])->except('show');

        Route::get('/newsletter', [NewsletterController::class, 'index'])->name('newsletter.index');
        Route::delete('/newsletter/{newsletter:id}', [NewsletterController::class, 'destroy'])->name('newsletter.destroy');

        Route::get('/requestContributor', [UserController::class, 'requestContributor'])->name('requestContributor.index');
        Route::delete('/requestContributor/{requestContributor:id}', [UserController::class, 'destroyRequestContributor'])->name('requestContributor.destroy');
    });

    Route::middleware(['auth', 'verified', 'role:superadmin,admin,writer'])->group(function () {
        // Posts
        Route::post('/posts/generateSlug', [PostController::class, 'generateSlug'])->name('posts.generateSlug');
        Route::get('/posts/generateAiContent', [PostController::class, 'generateArticle'])->name('generateAiContent');
        Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
        Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
        Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
        Route::get('/posts/{post:slug}/edit', [PostController::class, 'edit'])->name('posts.edit');
        Route::get('/posts/{post:slug}/preview', [PostController::class, 'preview'])->name('posts.preview');
        Route::put('/posts/{post:slug}', [PostController::class, 'update'])->name('posts.update');
        Route::delete('/posts/{post:slug}', [PostController::class, 'destroy'])->name('posts.destroy');
        Route::delete('/posts/{post:slug}/permanent', [PostController::class, 'permanentlyDelete'])->name('posts.destroy-permanent');
        Route::post('/posts/restore/{slug}', [PostController::class, 'restore'])->name('posts.restore');

        Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');

        // Statistics
        Route::get('/posts/stats/6months', [ArticleViewController::class, 'getViewsLast6Months'])->name('posts.statslast6months');
        Route::get('/stats/posts', [ArticleViewController::class, 'getArticleStats'])->name('posts.statsview');
        Route::get('/stats/posts/locations', [ArticleViewController::class, 'statsByLocation'])->name('posts.statslocation');
        Route::get('/stats/posts/{article:slug}', [ArticleViewController::class, 'statsPerArticle'])->name('posts.statsdetail');
    });

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::post('/requests-contributors', [UserController::class, 'storeRequestContributor'])->name('requestsContributors');
        Route::post('/requests-contributors/confirm', [UserController::class, 'confirmCodeContributor'])->name('confirmCodeContributor');

        Route::get('/my-comments', [CommentController::class, 'mycomments'])->name('mycomments.index');
        Route::delete('/comments/{comment:id}', [CommentController::class, 'destroy'])->name('comment.destroy');

        // Profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::patch('/photo-profile', [ProfileController::class, 'updatePhoto'])->name('profile.photo-update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/comments/{post:slug}', [CommentController::class, 'store'])->name('comment.store');
});

Route::get('/admin', function () {
    return redirect('/dashboard');
});

// Blog Post
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/blog', [ArticleController::class, 'index'])->name('article.index');
Route::get('/blog/recent-posts', fn() => redirect()->route('article.index'));
Route::get('/blog/popular', [ArticleController::class, 'popularPost'])->name('article.popular');
Route::get('/blog/popular-post', fn() => redirect()->route('article.popular'));
Route::get('/blog/tags/{slug}', [ArticleController::class, 'articlesByTag'])->name('article.tag');
Route::get('/blog/categories/{slug}', [ArticleController::class, 'articlesByCategory'])->name('article.category');
Route::get('/blog/users/{username}', [ArticleController::class, 'articlesByUser'])->name('article.user');
Route::get('/blog/archive/{year}', [ArticleController::class, 'articlesByYear'])->name('article.year');
Route::get('/blog/archive/{year}/{month}', [ArticleController::class, 'articlesByMonth'])->name('article.month');
Route::get('/blog/{year}/{slug}', [ArticleController::class, 'show'])->name('article.show');

Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');

Route::get('/dashboard/pages/{id}/load-project', [PageController::class, 'loadProject'])->name('pages.loadproject');

Route::get('/p/{page:slug}', [PageController::class, 'show'])->name('page.show');


require __DIR__ . '/auth.php';
