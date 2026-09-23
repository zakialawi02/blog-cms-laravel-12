<?php

use App\Models\Article;
use App\Models\ArticleView;
use App\Models\User;
use Database\Seeders\WebSettingsSeeder;

/**
 * Regression tests for the same defect class as Sentry BLOG-CMS-LARAVEL-3Z.
 *
 * `articles.user_id` is nullable and its foreign key is declared with
 * nullOnDelete(), so deleting an author leaves their articles with a NULL user.
 * Every place that assumed `$article->user` is present raised
 * "Attempt to read property ... on null" instead of rendering the page.
 */

beforeEach(function () {
    $this->seed(WebSettingsSeeder::class);
});

/**
 * A published article whose author has been removed (user_id set to null by the
 * nullOnDelete foreign key).
 */
function articleWithoutAuthor(array $attributes = []): Article
{
    return Article::factory()->create(array_merge([
        'user_id' => null,
        'status' => 'published',
        'published_at' => now()->subDay(),
    ], $attributes));
}

it('renders the home page when a listed post has no author', function () {
    articleWithoutAuthor(['title' => 'Tanpa penulis']);

    $this->get('/')->assertOk();
});

it('renders the post listing when a listed post has no author', function () {
    articleWithoutAuthor();

    $this->get('/blog')->assertOk();
});

it('renders the popular page when a listed post has no author', function () {
    $article = articleWithoutAuthor();
    ArticleView::factory()->create(['article_id' => $article->id]);

    $this->get('/blog/popular')->assertOk();
});

it('renders the archive page when a listed post has no author', function () {
    $article = articleWithoutAuthor();

    $this->get('/blog/archive/' . $article->published_at->year)->assertOk();
    $this->get('/blog/archive/' . $article->published_at->year . '/' . $article->published_at->format('m'))->assertOk();
});

it('renders a single post whose author has been deleted', function () {
    $article = articleWithoutAuthor();

    $this->get('/blog/' . $article->published_at->year . '/' . $article->slug)->assertOk();
});

it('serves the public article API when a post has no author', function () {
    articleWithoutAuthor(['title' => 'Tanpa penulis']);

    $this->getJson('/api/v1/articles')
        ->assertOk()
        ->assertJsonStructure(['data' => ['*' => ['title', 'author']]])
        ->assertJsonPath('data.0.title', 'Tanpa penulis')
        ->assertJsonPath('data.0.author', null);
});

it('still renders the author link for posts that do have an author', function () {
    $writer = User::factory()->create(['role' => 'writer', 'username' => 'penulis-aktif']);
    Article::factory()->create([
        'user_id' => $writer->id,
        'status' => 'published',
        'published_at' => now()->subDay(),
    ]);

    $this->get('/blog')->assertOk()->assertSee('penulis-aktif');
});

it('renders the blog when the author has been soft-deleted', function () {
    $writer = User::factory()->create(['role' => 'writer', 'username' => 'penulis-terhapus']);
    $article = Article::factory()->create([
        'user_id' => $writer->id,
        'status' => 'published',
        'published_at' => now()->subDay(),
    ]);

    // UserController::destroy() soft-deletes the author, so the row stays in the
    // DB and the nullOnDelete() foreign key never fires. Eloquent's belongsTo
    // excludes trashed parents, which makes $article->user null at read time.
    $writer->delete();

    expect($writer->trashed())->toBeTrue();
    expect($article->fresh()->user)->toBeNull();

    $this->get('/')->assertOk();
    $this->get('/blog/' . $article->published_at->year . '/' . $article->slug)->assertOk();
});
