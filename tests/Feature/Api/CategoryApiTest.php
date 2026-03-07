<?php

use App\Models\Category;
use App\Models\User;

function categoryAuthHeader(): array
{
    $user = User::factory()->create();
    $token = $user->createToken('authToken')->plainTextToken;

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

it('rejects unauthenticated access to categories endpoints', function () {
    $response = $this->getJson('/api/v1/categories');

    $response->assertUnauthorized();
});
