<?php

use App\Models\Category;
use App\Models\User;
use App\Enums\TokenAbility;

function categoryAuthHeader(): array
{
    $user = User::factory()->create(['role' => 'admin']);
    $token = $user->createToken('authToken', TokenAbility::abilitiesForRole('admin'))->plainTextToken;

    return ['Authorization' => 'Bearer ' . $token];
}

it('creates a category', function () {
    $response = $this
        ->withHeaders(categoryAuthHeader())
        ->postJson('/api/v1/categories', [
            'category' => 'Testing',
            'slug' => 'testing',
        ]);

    $response
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Category created successfully')
        ->assertJsonPath('data.category', 'Testing')
        ->assertJsonPath('data.slug', 'testing');

    $this->assertDatabaseHas('categories', [
        'category' => 'Testing',
        'slug' => 'testing',
    ]);
});

it('returns a paginated list of categories for an authenticated user', function () {
    Category::factory()->create(['category' => 'Laravel', 'slug' => 'laravel']);
    Category::factory()->create(['category' => 'PHP', 'slug' => 'php']);

    $response = $this
        ->withHeaders(categoryAuthHeader())
        ->getJson('/api/v1/categories?search=Laravel&limit=1');

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Categories retrieved successfully')
        ->assertJsonPath('data.0.category', 'Laravel')
        ->assertJsonPath('meta.per_page', 1)
        ->assertJsonCount(1, 'data');
});

it('shows a category by slug', function () {
    $category = Category::factory()->create([
        'category' => 'Laravel',
        'slug' => 'laravel',
    ]);

    $response = $this
        ->withHeaders(categoryAuthHeader())
        ->getJson('/api/v1/categories/' . $category->slug);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Category retrieved successfully')
        ->assertJsonPath('data.id', $category->id)
        ->assertJsonPath('data.slug', $category->slug);
});

it('updates a category by slug', function () {
    $category = Category::factory()->create([
        'category' => 'Old Category',
        'slug' => 'old-category',
    ]);

    $response = $this
        ->withHeaders(categoryAuthHeader())
        ->putJson('/api/v1/categories/' . $category->slug, [
            'category' => 'New Category',
            'slug' => 'new-category',
        ]);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Category updated successfully')
        ->assertJsonPath('data.category', 'New Category')
        ->assertJsonPath('data.slug', 'new-category');

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'category' => 'New Category',
        'slug' => 'new-category',
    ]);
});

it('deletes a category by slug', function () {
    $category = Category::factory()->create([
        'slug' => 'delete-me',
    ]);

    $response = $this
        ->withHeaders(categoryAuthHeader())
        ->deleteJson('/api/v1/categories/' . $category->slug);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Category deleted successfully');

    $this->assertDatabaseMissing('categories', [
        'id' => $category->id,
    ]);
});

it('allows public access to categories list', function () {
    $response = $this->getJson('/api/v1/categories');

    $response->assertOk();
});

it('rejects public access to category detail', function () {
    $category = Category::factory()->create();
    $response = $this->getJson('/api/v1/categories/' . $category->slug);

    $response->assertUnauthorized();
});

it('allows non-admin logged-in user to access list categories endpoint', function () {
    $user = User::factory()->create(['role' => 'user']);
    $token = $user->createToken('authToken', TokenAbility::abilitiesForRole('user'))->plainTextToken;

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $token])
        ->getJson('/api/v1/categories');

    $response
        ->assertOk()
        ->assertJsonPath('success', true);
});

it('rejects non-admin user from accessing restricted categories endpoint (GET Detail)', function () {
    $user = User::factory()->create(['role' => 'user']);
    $token = $user->createToken('authToken', TokenAbility::abilitiesForRole('user'))->plainTextToken;
    $category = Category::factory()->create();

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $token])
        ->getJson('/api/v1/categories/' . $category->slug);

    $response
        ->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Forbidden: You do not have the required permission.');
});

it('rejects non-admin user from accessing restricted categories endpoint (POST)', function () {
    $user = User::factory()->create(['role' => 'user']);
    $token = $user->createToken('authToken', TokenAbility::abilitiesForRole('user'))->plainTextToken;

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $token])
        ->postJson('/api/v1/categories', [
            'category' => 'Forbidden Create',
            'slug' => 'forbidden-create',
        ]);

    $response
        ->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Forbidden: You do not have the required permission.');
});

it('allows admin user to access restricted categories endpoint (POST)', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('authToken', TokenAbility::abilitiesForRole('admin'))->plainTextToken;

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $token])
        ->postJson('/api/v1/categories', [
            'category' => 'Admin Create',
            'slug' => 'admin-create',
        ]);

    $response
        ->assertCreated()
        ->assertJsonPath('success', true);
});

it('allows superadmin user to access restricted categories endpoint (POST)', function () {
    $superadmin = User::factory()->create(['role' => 'superadmin']);
    $token = $superadmin->createToken('authToken', TokenAbility::abilitiesForRole('superadmin'))->plainTextToken;

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $token])
        ->postJson('/api/v1/categories', [
            'category' => 'Superadmin Create',
            'slug' => 'superadmin-create',
        ]);

    $response
        ->assertCreated()
        ->assertJsonPath('success', true);
});
