<?php

use App\Models\Article;
use App\Models\User;

/**
 * Regression test for Sentry issue BLOG-CMS-LARAVEL-3Y.
 *
 * `articles.user_id` is nullable and its foreign key is declared with
 * nullOnDelete(), so deleting an author legitimately leaves their articles
 * without a user. PostController::index() dereferenced `$data->user->username`
 * without a guard in the "user" and "author" datatables columns, so
 * GET /dashboard/posts answered HTTP 500 with
 * "Attempt to read property \"username\" on null".
 */

it('lists posts whose author has been deleted', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Article::factory()->create([
        'user_id' => null,
        'title' => 'Artikel tanpa penulis',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $response = $this->actingAs($admin)
        ->get('/dashboard/posts', ['X-Requested-With' => 'XMLHttpRequest']);

    $response->assertOk();
    $response->assertSee('Artikel tanpa penulis');
});

it('still shows the username of posts that do have an author', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $writer = User::factory()->create(['role' => 'writer', 'username' => 'penulis-aktif']);
    Article::factory()->create([
        'user_id' => $writer->id,
        'title' => 'Artikel dengan penulis',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $response = $this->actingAs($admin)
        ->get('/dashboard/posts', ['X-Requested-With' => 'XMLHttpRequest']);

    $response->assertOk();
    $response->assertSee('penulis-aktif');
});
