<?php

use App\Models\Article;
use App\Models\User;
use App\Enums\TokenAbility;
use Illuminate\Support\Facades\Storage;

// Helpers — create user+token helpers local to this file
function articleDeleteAuth(string $role): array
{
    $user = User::factory()->create(['role' => $role]);
    $token = $user->createToken('t', TokenAbility::abilitiesForRole($role))->plainTextToken;
    return [$user, $token];
}

// 401 — wrong API key
it('rejects delete without valid API key', function () {
    [$user, $token] = articleDeleteAuth('admin');
    $article = Article::factory()->create(['user_id' => $user->id]);
    $this->withHeaders(['Authorization' => "Bearer $token", 'X-API-Key' => 'wrong-key'])
        ->deleteJson("/api/v1/articles/{$article->slug}")
        ->assertUnauthorized();
});

// 401 — no auth token
it('rejects delete without auth token', function () {
    $article = Article::factory()->create();
    $this->deleteJson("/api/v1/articles/{$article->slug}")
        ->assertUnauthorized();
});

// 403 — user role has no delete ability
it('rejects delete for user without ability', function () {
    [$user, $token] = articleDeleteAuth('user');
    $article = Article::factory()->create(['user_id' => $user->id]);
    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->deleteJson("/api/v1/articles/{$article->slug}")
        ->assertForbidden();
});

// 404 — article not found
it('returns 404 for non-existent article on delete', function () {
    [$user, $token] = articleDeleteAuth('admin');
    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->deleteJson("/api/v1/articles/non-existent-slug-99999")
        ->assertNotFound();
});

// Writer can soft-delete own article (by slug)
it('allows writer to soft-delete own article by slug', function () {
    [$writer, $token] = articleDeleteAuth('writer');
    $article = Article::factory()->create(['user_id' => $writer->id, 'slug' => 'writer-own-article']);
    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->deleteJson("/api/v1/articles/{$article->slug}")
        ->assertOk()->assertJsonPath('success', true);
    expect(Article::withTrashed()->find($article->id)->trashed())->toBeTrue();
});

// Writer can soft-delete own article by id
it('allows writer to soft-delete own article by id', function () {
    [$writer, $token] = articleDeleteAuth('writer');
    $article = Article::factory()->create(['user_id' => $writer->id]);
    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->deleteJson("/api/v1/articles/{$article->id}")
        ->assertOk();
    expect(Article::withTrashed()->find($article->id)->trashed())->toBeTrue();
});

// Writer cannot delete others article
it('rejects writer deleting others article', function () {
    [$writer, $token] = articleDeleteAuth('writer');
    $other = User::factory()->create(['role' => 'writer']);
    $article = Article::factory()->create(['user_id' => $other->id]);
    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->deleteJson("/api/v1/articles/{$article->slug}")
        ->assertForbidden();
    expect(Article::find($article->id))->not->toBeNull();
});

// Admin can delete others article
it('allows admin to soft-delete others article', function () {
    [$admin, $token] = articleDeleteAuth('admin');
    $other = User::factory()->create(['role' => 'writer']);
    $article = Article::factory()->create(['user_id' => $other->id]);
    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->deleteJson("/api/v1/articles/{$article->slug}")
        ->assertOk();
    expect(Article::withTrashed()->find($article->id)->trashed())->toBeTrue();
});

// Second delete on already trashed -> 404
it('returns 404 when soft-deleting already trashed article', function () {
    [$writer, $token] = articleDeleteAuth('writer');
    $article = Article::factory()->create(['user_id' => $writer->id]);
    $this->withHeaders(['Authorization' => "Bearer $token"])->deleteJson("/api/v1/articles/{$article->slug}")->assertOk();
    $this->withHeaders(['Authorization' => "Bearer $token"])->deleteJson("/api/v1/articles/{$article->slug}")->assertNotFound();
});

// Restore — owner can restore
it('allows owner to restore trashed article', function () {
    [$writer, $token] = articleDeleteAuth('writer');
    $article = Article::factory()->create(['user_id' => $writer->id]);
    $this->withHeaders(['Authorization' => "Bearer $token"])->deleteJson("/api/v1/articles/{$article->slug}")->assertOk();
    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->postJson("/api/v1/articles/{$article->slug}/restore")
        ->assertOk()->assertJsonPath('success', true);
    expect(Article::find($article->id))->not->toBeNull();
    expect(Article::find($article->id)->trashed())->toBeFalse();
});

// Restore — non-owner writer cannot restore
it('rejects non-owner writer from restoring', function () {
    $other = User::factory()->create(['role' => 'writer']);
    $article2 = Article::factory()->create(['user_id' => $other->id, 'slug' => 'restore-target']);
    [$admin, $adminToken] = articleDeleteAuth('admin');
    $this->withHeaders(['Authorization' => "Bearer $adminToken"])->deleteJson("/api/v1/articles/{$article2->slug}")->assertOk();
    // Use actingAs to avoid header pollution
    [$writer, $token] = articleDeleteAuth('writer');
    \Laravel\Sanctum\Sanctum::actingAs($writer, ['article.delete']);
    $this->postJson("/api/v1/articles/{$article2->slug}/restore")
        ->assertForbidden();
});

// Restore — 404 if not in trash
it('returns 404 when restoring non-trashed article', function () {
    [$admin, $token] = articleDeleteAuth('admin');
    $article = Article::factory()->create(['user_id' => $admin->id]);
    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->postJson("/api/v1/articles/{$article->slug}/restore")
        ->assertNotFound();
});

// Permanent — owner/superadmin can force delete, cleans files
it('allows owner to permanently delete trashed article', function () {
    Storage::fake('public');
    [$writer, $token] = articleDeleteAuth('writer');
    // Create article with fake cover paths (to test file cleanup path exists handling)
    $article = Article::factory()->create(['user_id' => $writer->id, 'cover' => '/storage/media/img/test_small.jpg', 'cover_large' => '/storage/media/img/test_large.jpg']);
    $this->withHeaders(['Authorization' => "Bearer $token"])->deleteJson("/api/v1/articles/{$article->slug}")->assertOk();
    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->deleteJson("/api/v1/articles/{$article->slug}/permanent")
        ->assertOk()->assertJsonPath('success', true);
    expect(Article::withTrashed()->find($article->id))->toBeNull();
});

// Permanent — non-owner writer cannot force delete
it('rejects non-owner writer from permanent delete', function () {
    [$writer, $token] = articleDeleteAuth('writer');
    $other = User::factory()->create(['role' => 'writer']);
    $article = Article::factory()->create(['user_id' => $other->id, 'slug' => 'perm-target']);
    [$admin, $adminToken] = articleDeleteAuth('admin');
    $this->withHeaders(['Authorization' => "Bearer $adminToken"])->deleteJson("/api/v1/articles/{$article->slug}")->assertOk();
    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->deleteJson("/api/v1/articles/{$article->slug}/permanent")
        ->assertForbidden();
});

// Permanent — 404 if not in trash (active article)
it('returns 404 when permanently deleting active article', function () {
    [$admin, $token] = articleDeleteAuth('admin');
    $article = Article::factory()->create(['user_id' => $admin->id]);
    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->deleteJson("/api/v1/articles/{$article->slug}/permanent")
        ->assertNotFound();
});

// Permanent — admin (not owner, not superadmin) cannot force delete per isOwnedOrSuperadmin
it('rejects admin non-owner from permanent delete', function () {
    [$admin, $token] = articleDeleteAuth('admin');
    $other = User::factory()->create(['role' => 'writer']);
    $article = Article::factory()->create(['user_id' => $other->id, 'slug' => 'admin-perm-target']);
    // Need superadmin or owner; admin non-owner should be rejected — but admin soft-deletes first
    $this->withHeaders(['Authorization' => "Bearer $token"])->deleteJson("/api/v1/articles/{$article->slug}")->assertOk();
    // Now admin tries permanent (non-owner, not superadmin) -> 403
    $this->withHeaders(['Authorization' => "Bearer $token"])
        ->deleteJson("/api/v1/articles/{$article->slug}/permanent")
        ->assertForbidden();
});

// Superadmin can permanent delete others
it('allows superadmin to permanently delete others trashed article', function () {
    $other = User::factory()->create(['role' => 'writer']);
    $article = Article::factory()->create(['user_id' => $other->id, 'slug' => 'superadmin-perm-target']);
    [$admin, $adminToken] = articleDeleteAuth('admin');
    $this->withHeaders(['Authorization' => "Bearer $adminToken"])->deleteJson("/api/v1/articles/{$article->slug}")->assertOk();
    [$superadmin, $token] = articleDeleteAuth('superadmin');
    \Laravel\Sanctum\Sanctum::actingAs($superadmin, ['article.delete', 'article.manage']);
    $this->deleteJson("/api/v1/articles/{$article->slug}/permanent")
        ->assertOk();
    expect(Article::withTrashed()->find($article->id))->toBeNull();
});
