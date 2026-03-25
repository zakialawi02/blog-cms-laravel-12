<?php

use App\Models\User;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can fetch comments for an article', function () {
    $article = Article::factory()->create();
    Comment::factory()->count(3)->create(['article_id' => $article->id]);

    $response = $this->getJson("/api/v1/articles/{$article->slug}/comments");

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonCount(3, 'data');
});

it('supports pagination and sorting for comments', function () {
    $article = Article::factory()->create();
    Comment::factory()->create(['article_id' => $article->id, 'created_at' => now()->subDay(), 'content' => 'Oldest']);
    Comment::factory()->create(['article_id' => $article->id, 'created_at' => now(), 'content' => 'Newest']);

    // Test sorting
    $response = $this->getJson("/api/v1/articles/{$article->slug}/comments?sort=created_at&order=asc");
    $response->assertOk()
        ->assertJsonPath('data.0.content', 'Oldest');

    // Test limit/pagination
    Comment::factory()->count(15)->create(['article_id' => $article->id]);
    $response = $this->getJson("/api/v1/articles/{$article->slug}/comments?limit=5");
    $response->assertOk()
        ->assertJsonCount(5, 'data')
        ->assertJsonPath('meta.total', 17)
        ->assertJsonPath('meta.per_page', 5);
});

it('fetches only approved comments', function () {
    $article = Article::factory()->create();
    Comment::factory()->create(['article_id' => $article->id, 'is_approved' => true, 'content' => 'Approved']);
    Comment::factory()->create(['article_id' => $article->id, 'is_approved' => false, 'content' => 'Pending']);

    $response = $this->getJson("/api/v1/articles/{$article->slug}/comments");

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.content', 'Approved');
});

it('can fetch nested comments (replies)', function () {
    $article = Article::factory()->create();
    $parent = Comment::factory()->create(['article_id' => $article->id]);
    $reply = Comment::factory()->create(['article_id' => $article->id, 'parent_id' => $parent->id]);

    $response = $this->getJson("/api/v1/articles/{$article->slug}/comments");

    $response->assertOk()
        ->assertJsonCount(1, 'data') // Only top level
        ->assertJsonCount(1, 'data.0.replies') // Nested reply
        ->assertJsonPath('data.0.replies.0.id', $reply->id);
});

it('allows authenticated user to post a comment', function () {
    $user = User::factory()->create();
    $article = Article::factory()->create();

    $response = $this->actingAs($user)
        ->postJson("/api/v1/articles/{$article->slug}/comments", [
            'content' => 'This is a test comment',
        ]);

    $response->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.content', 'This is a test comment');

    $this->assertDatabaseHas('comments', [
        'article_id' => $article->id,
        'user_id' => $user->id,
        'content' => 'This is a test comment',
    ]);
});

it('allows authenticated user to post a reply', function () {
    $user = User::factory()->create();
    $article = Article::factory()->create();
    $parent = Comment::factory()->create(['article_id' => $article->id]);

    $response = $this->actingAs($user)
        ->postJson("/api/v1/articles/{$article->slug}/comments", [
            'content' => 'This is a reply',
            'parent_id' => $parent->id,
        ]);

    $response->assertCreated()
        ->assertJsonPath('data.parent_id', $parent->id);

    $this->assertDatabaseHas('comments', [
        'parent_id' => $parent->id,
        'content' => 'This is a reply',
    ]);
});

it('rejects posting a comment without authentication', function () {
    $article = Article::factory()->create();

    $response = $this->postJson("/api/v1/articles/{$article->slug}/comments", [
        'content' => 'Unauthorized comment',
    ]);

    $response->assertUnauthorized();
});

it('validates comment content', function () {
    $user = User::factory()->create();
    $article = Article::factory()->create();

    $response = $this->actingAs($user)
        ->postJson("/api/v1/articles/{$article->slug}/comments", [
            'content' => 'hi', // too short (min:3)
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['content']);
});

it('returns 404 for non-existent article', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson("/api/v1/articles/non-existent-slug/comments", [
            'content' => 'Some content',
        ]);

    $response->assertNotFound();
});
