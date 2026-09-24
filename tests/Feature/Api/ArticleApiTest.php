<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Enums\TokenAbility;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function articleAuthHeader(string $role = 'admin'): array
{
    $user = User::factory()->create(['role' => $role]);
    $token = $user->createToken('authToken', TokenAbility::abilitiesForRole($role))->plainTextToken;
    return ['token' => $token, 'user' => $user];
}

// 401 — missing X-API-Key (global middleware)
// In testing ALLOW_ALL_ORIGINS=true but X-API-Key is still required.
// ApiKeyMiddleware returns 401 when header missing regardless of allow_all.
it('rejects POST article without API key', function () {
    $writer = User::factory()->create(['role' => 'writer']);
    $token = $writer->createToken('t', TokenAbility::abilitiesForRole('writer'))->plainTextToken;
    // Send wrong API key explicitly
    $this->withHeaders(['Authorization' => "Bearer $token", 'X-API-Key' => 'wrong-key'])
        ->postJson('/api/v1/articles', ['title' => 'Hello World Test', 'content' => 'Some content here long enough'])
        ->assertUnauthorized();
});

// 401 — missing Sanctum token
it('rejects POST article without auth token', function () {
    $this->postJson('/api/v1/articles', ['title' => 'Hello World Test', 'content' => 'Some content here long enough'])
        ->assertUnauthorized();
});

// 403 — user role has no article ability
it('rejects POST article for user without ability', function () {
    $user = User::factory()->create(['role' => 'user']);
    $token = $user->createToken('t', TokenAbility::abilitiesForRole('user'))->plainTextToken;
    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->postJson('/api/v1/articles', ['title' => 'Hello World Test', 'content' => 'Some content here long enough'])
        ->assertForbidden();
});

// 422 — missing required fields
it('validates required fields', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('t', TokenAbility::abilitiesForRole('admin'))->plainTextToken;
    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->postJson('/api/v1/articles', [])
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonValidationErrors(['title', 'content']);
});

// 422 — title too short, slug duplicate
it('validates slug uniqueness', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('t', TokenAbility::abilitiesForRole('admin'))->plainTextToken;
    Article::factory()->create(['slug' => 'my-title', 'user_id' => $admin->id]);
    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->postJson('/api/v1/articles', ['title' => 'My Title', 'content' => 'Long enough content here yes', 'slug' => 'my-title'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['slug']);
});

// Writer -> pending
it('creates article as pending for writer with publish action', function () {
    $writer = User::factory()->create(['role' => 'writer']);
    $token = $writer->createToken('t', TokenAbility::abilitiesForRole('writer'))->plainTextToken;
    $category = Category::factory()->create();
    $response = $this->withHeaders(['Authorization' => "Bearer $token"])
        ->postJson('/api/v1/articles', [
            'title' => 'Writer Publish Attempt Title',
            'content' => 'This is a long enough content for validation to pass.',
            'category_id' => $category->id,
            'action' => 'publish',
        ]);
    $response->assertCreated()->assertJsonPath('success', true)->assertJsonPath('message', 'Article created and pending approval.');
    $this->assertDatabaseHas('articles', ['title' => 'Writer Publish Attempt Title', 'status' => 'pending', 'user_id' => $writer->id]);
    expect(Article::where('slug', 'writer-publish-attempt-title')->first()->published_at)->toBeNull();
});

// Admin -> published
it('creates article as published for admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('t', TokenAbility::abilitiesForRole('admin'))->plainTextToken;
    $response = $this->withHeaders(['Authorization' => "Bearer $token"])
        ->postJson('/api/v1/articles', [
            'title' => 'Admin Published Article Title',
            'content' => 'This is a long enough content for validation to pass.',
            'action' => 'publish',
        ]);
    $response->assertCreated()->assertJsonPath('success', true)->assertJsonPath('message', 'Article published successfully.');
    $article = Article::where('slug', 'admin-published-article-title')->first();
    expect($article->status)->toBe('published');
    expect($article->published_at)->not->toBeNull();
});

// Draft for any role
it('creates article as draft when action is draft', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('t', TokenAbility::abilitiesForRole('admin'))->plainTextToken;
    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->postJson('/api/v1/articles', [
            'title' => 'Draft Article Title Here',
            'content' => 'This is a long enough content for validation to pass.',
            'action' => 'draft',
        ])->assertCreated()->assertJsonPath('message', 'Article saved as draft.');
    expect(Article::where('slug', 'draft-article-title-here')->first()->status)->toBe('draft');
});

it('ignores user_id spoofing and uses auth user', function () {
    $writer = User::factory()->create(['role' => 'writer']);
    $other = User::factory()->create(['role' => 'admin']);
    $token = $writer->createToken('t', TokenAbility::abilitiesForRole('writer'))->plainTextToken;
    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->postJson('/api/v1/articles', [
            'title' => 'Spoof User Test Title',
            'content' => 'Long enough content for validation.',
            'user_id' => $other->id,
        ])->assertCreated();
    expect(Article::where('slug', 'spoof-user-test-title')->first()->user_id)->toBe($writer->id);
});

it('auto-generates slug when not provided', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('t', TokenAbility::abilitiesForRole('admin'))->plainTextToken;
    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->postJson('/api/v1/articles', [
            'title' => 'Auto Slug Generation Test',
            'content' => 'Long enough content for validation.',
        ])->assertCreated()->assertJsonPath('data.slug', 'auto-slug-generation-test');
});

it('strips is_featured for writer', function () {
    $writer = User::factory()->create(['role' => 'writer']);
    $tokenW = $writer->createToken('t', TokenAbility::abilitiesForRole('writer'))->plainTextToken;
    $this->withHeaders(['Authorization' => "Bearer $tokenW"])
        ->postJson('/api/v1/articles', [
            'title' => 'Writer Featured Attempt',
            'content' => 'Long enough content for validation.',
            'is_featured' => true,
        ])->assertCreated();
    expect(Article::where('slug', 'writer-featured-attempt')->first()->is_featured)->toBe(0);
});

it('respects is_featured for admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $tokenA = $admin->createToken('t', TokenAbility::abilitiesForRole('admin'))->plainTextToken;
    $this->withHeaders(['Authorization' => "Bearer $tokenA"])
        ->postJson('/api/v1/articles', [
            'title' => 'Admin Featured Success',
            'content' => 'Long enough content for validation.',
            'is_featured' => 1,
            'action' => 'publish',
        ])->assertCreated();
    $art = Article::where('slug', 'admin-featured-success')->first();
    expect((int) $art->is_featured)->toBe(1);
    expect($art->status)->toBe('published');
});

it('syncs tags from string array', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('t', TokenAbility::abilitiesForRole('admin'))->plainTextToken;
    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->postJson('/api/v1/articles', [
            'title' => 'Tagged Article String Array',
            'content' => 'Long enough content for validation.',
            'tags' => ['laravel', 'php'],
        ])->assertCreated();
    $article = Article::where('slug', 'tagged-article-string-array')->first();
    expect($article->tags)->toHaveCount(2);
});

it('syncs tags from object array', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('t', TokenAbility::abilitiesForRole('admin'))->plainTextToken;
    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->postJson('/api/v1/articles', [
            'title' => 'Tagged Article Object Array',
            'content' => 'Long enough content for validation.',
            'tags' => [['value' => 'laravel'], ['value' => 'php']],
        ])->assertCreated();
    $article = Article::where('slug', 'tagged-article-object-array')->first();
    expect($article->tags)->toHaveCount(2);
});

it('handles cover upload', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('t', TokenAbility::abilitiesForRole('admin'))->plainTextToken;
    $file = UploadedFile::fake()->image('cover.jpg', 800, 600);
    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->post('/api/v1/articles', [
            'title' => 'Cover Upload Article Title',
            'content' => 'Long enough content for validation.',
            'cover' => $file,
        ])->assertCreated();
    $article = Article::where('slug', 'cover-upload-article-title')->first();
    expect($article->cover)->not->toBeNull();
    expect($article->cover_large)->not->toBeNull();
});
