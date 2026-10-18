<?php

use App\Models\Article;
use App\Models\Category;
use App\Services\ArticleService;
use Database\Seeders\WebSettingsSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Regression test for Sentry issue BLOG-CMS-LARAVEL-46.
 *
 * GET /blog/tags/{slug} spent 2.5s in
 *   select * from `articles` ... order by RAND() limit 6
 * (span ecea5cd279ab44fe, trace a09aafefa7f84c4b97e6f76d94674f33).
 *
 * ORDER BY RAND() makes the database scan every row, copy the whole set into a
 * temporary table and sort it before discarding all but a handful, so each
 * random-article widget costs a full table read per page view.
 */

beforeEach(function () {
    $this->seed(WebSettingsSeeder::class);
});

function scopedCategory(): Category
{
    return Category::factory()->create(['slug' => 'sentry-'.Str::lower(Str::random(12))]);
}

function scopedArticle(Category $category, array $attributes = []): Article
{
    return Article::factory()->create(array_merge([
        'category_id' => $category->id,
        'status' => 'published',
        'published_at' => now()->subDay(),
        'is_featured' => false,
    ], $attributes));
}

function loggedQueries(): string
{
    return strtolower(implode("\n", array_map(fn ($entry) => $entry['query'], DB::getQueryLog())));
}

it('picks random posts without sorting the whole articles table', function () {
    $category = scopedCategory();
    foreach (range(1, 12) as $i) {
        scopedArticle($category, ['title' => "Random post {$i}"]);
    }

    DB::flushQueryLog();
    DB::enableQueryLog();
    $articles = app(ArticleService::class)->getRandomArticles(6, $category->slug);
    $queries = loggedQueries();
    DB::disableQueryLog();

    expect($articles)->toHaveCount(6)
        ->and($queries)->not->toBeEmpty()
        ->and($queries)->not->toContain('rand()');
});

it('fills missing featured posts without sorting the whole articles table', function () {
    $category = scopedCategory();
    foreach (range(1, 12) as $i) {
        scopedArticle($category, ['title' => "Filler post {$i}"]);
    }

    DB::flushQueryLog();
    DB::enableQueryLog();
    $articles = app(ArticleService::class)->getFeaturedArticles(5);
    $queries = loggedQueries();
    DB::disableQueryLog();

    expect($articles)->not->toBeEmpty()
        ->and($queries)->not->toBeEmpty()
        ->and($queries)->not->toContain('rand()');
});

it('returns only published posts, without duplicates, when picking randomly', function () {
    $category = scopedCategory();
    foreach (range(1, 12) as $i) {
        scopedArticle($category, ['title' => "Visible {$i}"]);
    }
    scopedArticle($category, ['title' => 'Hidden draft', 'status' => 'draft', 'published_at' => null]);
    scopedArticle($category, ['title' => 'Hidden future', 'published_at' => now()->addDay()]);
    scopedArticle($category, ['title' => 'Hidden deleted', 'deleted_at' => now()]);

    $articles = app(ArticleService::class)->getRandomArticles(6, $category->slug);

    expect($articles)->toHaveCount(6)
        ->and($articles->pluck('id')->unique())->toHaveCount(6)
        ->and($articles->pluck('title'))->not->toContain('Hidden draft')
        ->and($articles->pluck('title'))->not->toContain('Hidden future')
        ->and($articles->pluck('title'))->not->toContain('Hidden deleted');
});

it('returns every available post when fewer exist than requested', function () {
    $category = scopedCategory();
    scopedArticle($category, ['title' => 'Only one']);
    scopedArticle($category, ['title' => 'Only two']);
    scopedArticle($category, ['title' => 'Only three']);

    expect(app(ArticleService::class)->getRandomArticles(6, $category->slug))->toHaveCount(3);
});

it('returns an empty result when nothing is published', function () {
    $category = scopedCategory();

    expect(app(ArticleService::class)->getRandomArticles(6, $category->slug))->toHaveCount(0);
});

it('runs against the testing environment, never production', function () {
    // A regression guard: a stray APP_ENV (or any variable PHPUnit will not
    // override because it is already set) silently points the whole suite at
    // the production database, where seeded rows break unrelated tests and
    // RefreshDatabase is allowed to run migrations.
    expect(app()->environment())->toBe('testing')
        ->and(config('database.connections.mysql.database'))->not->toBe('sql_zakialawi_com');
});
