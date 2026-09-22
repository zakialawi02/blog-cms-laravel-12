<?php

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;

it('menolak komentar induk yang berasal dari artikel lain', function () {
    $user = User::factory()->create(['role' => 'writer']);

    $articleA = Article::factory()->create(['status' => 'published', 'published_at' => now()->subDay()]);
    $articleB = Article::factory()->create(['status' => 'published', 'published_at' => now()->subDay()]);

    $parentInOtherArticle = Comment::factory()->create([
        'article_id' => $articleB->id,
        'user_id' => $user->id,
    ]);

    $this->actingAs($user, 'sanctum')
        ->postJson("/api/v1/articles/{$articleA->slug}/comments", [
            'content' => 'Balasan lintas artikel',
            'parent_id' => $parentInOtherArticle->id,
        ])
        ->assertStatus(422);

    expect(Comment::where('content', 'Balasan lintas artikel')->exists())->toBeFalse();
});

it('menerima komentar induk dari artikel yang sama', function () {
    $user = User::factory()->create(['role' => 'writer']);
    $article = Article::factory()->create(['status' => 'published', 'published_at' => now()->subDay()]);

    $parent = Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $user->id,
    ]);

    $this->actingAs($user, 'sanctum')
        ->postJson("/api/v1/articles/{$article->slug}/comments", [
            'content' => 'Balasan sah',
            'parent_id' => $parent->id,
        ])
        ->assertCreated();

    expect(Comment::where('content', 'Balasan sah')->where('parent_id', $parent->id)->exists())->toBeTrue();
});

it('menolak parent_id yang tidak ada', function () {
    $user = User::factory()->create(['role' => 'writer']);
    $article = Article::factory()->create(['status' => 'published', 'published_at' => now()->subDay()]);

    $this->actingAs($user, 'sanctum')
        ->postJson("/api/v1/articles/{$article->slug}/comments", [
            'content' => 'Balasan tanpa induk nyata',
            'parent_id' => 999999,
        ])
        ->assertStatus(422);
});
