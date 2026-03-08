<?php

use App\Models\User;
use App\Models\Tag;
use App\Enums\TokenAbility;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows public access to tags list', function () {
    $response = $this->getJson('/api/v1/tags');

    $response->assertOk();
});

it('rejects public access to tag detail', function () {
    $tag = Tag::factory()->create();
    $response = $this->getJson('/api/v1/tags/' . $tag->slug);

    $response->assertUnauthorized();
});

it('allows non-admin logged-in user to access list tags endpoint', function () {
    $user = User::factory()->create(['role' => 'user']);
    $token = $user->createToken('authToken', TokenAbility::abilitiesForRole('user'))->plainTextToken;

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $token])
        ->getJson('/api/v1/tags');

    $response
        ->assertOk()
        ->assertJsonPath('success', true);
});

it('rejects non-admin user from accessing restricted tags endpoint (GET Detail)', function () {
    $user = User::factory()->create(['role' => 'user']);
    $token = $user->createToken('authToken', TokenAbility::abilitiesForRole('user'))->plainTextToken;
    $tag = Tag::factory()->create();

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $token])
        ->getJson('/api/v1/tags/' . $tag->slug);

    $response
        ->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Forbidden: You do not have the required permission.');
});

it('rejects non-admin user from accessing restricted tags endpoint (POST)', function () {
    $user = User::factory()->create(['role' => 'user']);
    $token = $user->createToken('authToken', TokenAbility::abilitiesForRole('user'))->plainTextToken;

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $token])
        ->postJson('/api/v1/tags', [
            'tag_name' => 'Forbidden Create',
            'slug' => 'forbidden-create',
        ]);

    $response
        ->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Forbidden: You do not have the required permission.');
});

it('allows admin user to access restricted tags endpoint (POST)', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('authToken', TokenAbility::abilitiesForRole('admin'))->plainTextToken;

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $token])
        ->postJson('/api/v1/tags', [
            'tag_name' => 'Admin Create',
            'slug' => 'admin-create',
        ]);

    $response
        ->assertCreated()
        ->assertJsonPath('success', true);
});

it('allows superadmin user to access restricted tags endpoint (POST)', function () {
    $superadmin = User::factory()->create(['role' => 'superadmin']);
    $token = $superadmin->createToken('authToken', TokenAbility::abilitiesForRole('superadmin'))->plainTextToken;

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $token])
        ->postJson('/api/v1/tags', [
            'tag_name' => 'Superadmin Create',
            'slug' => 'superadmin-create',
        ]);

    $response
        ->assertCreated()
        ->assertJsonPath('success', true);
});

it('allows writer user to access tag creation endpoint (POST)', function () {
    $writer = User::factory()->create(['role' => 'writer']);
    $token = $writer->createToken('authToken', TokenAbility::abilitiesForRole('writer'))->plainTextToken;

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $token])
        ->postJson('/api/v1/tags', [
            'tag_name' => 'Writer Create',
            'slug' => 'writer-create',
        ]);

    $response
        ->assertCreated()
        ->assertJsonPath('success', true);
});

it('rejects writer user from accessing restricted tags endpoint (PUT)', function () {
    $writer = User::factory()->create(['role' => 'writer']);
    $token = $writer->createToken('authToken', TokenAbility::abilitiesForRole('writer'))->plainTextToken;
    $tag = Tag::factory()->create();

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $token])
        ->putJson('/api/v1/tags/' . $tag->slug, [
            'tag_name' => 'Writer Update',
            'slug' => 'writer-update',
        ]);

    $response
        ->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Forbidden: You do not have the required permission.');
});

it('returns paginated list of tags', function () {
    Tag::factory()->count(15)->create();

    $response = $this->getJson('/api/v1/tags?limit=5');

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonCount(5, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'tag_name', 'slug', 'created_at', 'updated_at']
            ],
            'links',
            'meta',
        ]);
});

it('searches tags correctly', function () {
    Tag::factory()->create(['tag_name' => 'Laravel Framework', 'slug' => 'laravel-framework']);
    Tag::factory()->create(['tag_name' => 'VueJS Frontend', 'slug' => 'vuejs-frontend']);

    $response = $this->getJson('/api/v1/tags?search=Laravel');

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.tag_name', 'Laravel Framework');
});

it('can fetch specific tag by slug', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('authToken', TokenAbility::abilitiesForRole('admin'))->plainTextToken;

    $tag = Tag::factory()->create();

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $token])
        ->getJson('/api/v1/tags/' . $tag->slug);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.id', $tag->id);
});

it('can update specific tag', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('authToken', TokenAbility::abilitiesForRole('admin'))->plainTextToken;

    $tag = Tag::factory()->create();

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $token])
        ->putJson('/api/v1/tags/' . $tag->slug, [
            'tag_name' => 'Updated Name',
            'slug' => 'updated-name',
        ]);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.tag_name', 'Updated Name');
});

it('can delete specific tag', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('authToken', TokenAbility::abilitiesForRole('admin'))->plainTextToken;

    $tag = Tag::factory()->create();

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $token])
        ->deleteJson('/api/v1/tags/' . $tag->slug);

    if ($response->status() !== 200) {
        dd($response->json());
    }

    $response
        ->assertOk()
        ->assertJsonPath('success', true);

    $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
});
