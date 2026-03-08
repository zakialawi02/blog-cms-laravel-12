<?php

use App\Models\User;
use App\Enums\TokenAbility;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

function usersAuthHeader(): array
{
    $user = User::factory()->create(['role' => 'admin']);
    $token = $user->createToken('authToken', TokenAbility::abilitiesForRole('admin'))->plainTextToken;

    return ['Authorization' => 'Bearer ' . $token];
}

it('creates a user', function () {
    $response = $this
        ->withHeaders(usersAuthHeader())
        ->postJson('/api/v1/users', [
            'name' => 'Managed User',
            'username' => 'manageduser',
            'role' => 'writer',
            'email' => 'managed@example.com',
            'password' => 'Password123!',
        ]);

    $response
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'User created successfully.')
        ->assertJsonPath('data.username', 'manageduser')
        ->assertJsonPath('data.role', 'writer');

    $this->assertDatabaseHas('users', [
        'email' => 'managed@example.com',
        'username' => 'manageduser',
        'role' => 'writer',
    ]);

    expect(Hash::check('Password123!', User::where('email', 'managed@example.com')->firstOrFail()->password))->toBeTrue();
});

it('rejects invalid data when creating a user', function () {
    User::factory()->create([
        'username' => 'manageduser',
        'email' => 'managed@example.com',
    ]);

    $response = $this
        ->withHeaders(usersAuthHeader())
        ->postJson('/api/v1/users', [
            'name' => 'A',
            'username' => 'manageduser',
            'role' => 'invalid-role',
            'email' => 'managed@example.com',
            'password' => '123',
        ]);

    $response
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Validation failed.');
});

it('returns a paginated filtered list of users', function () {
    User::factory()->create([
        'name' => 'Alpha User',
        'username' => 'alphauser',
        'email' => 'alpha@example.com',
    ]);

    User::factory()->create([
        'name' => 'Beta User',
        'username' => 'betauser',
        'email' => 'beta@example.com',
    ]);

    $response = $this
        ->withHeaders(usersAuthHeader())
        ->getJson('/api/v1/users?search=alpha&sort=name&direction=asc&limit=1');

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'List of all users')
        ->assertJsonPath('data.0.email', 'alpha@example.com')
        ->assertJsonPath('meta.per_page', 1)
        ->assertJsonCount(1, 'data');
});

it('shows a single user', function () {
    $user = User::factory()->create();

    $response = $this
        ->withHeaders(usersAuthHeader())
        ->getJson('/api/v1/users/' . $user->id);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.email', $user->email);
});

it('updates a user including verification timestamp and password', function () {
    $user = User::factory()->unverified()->create([
        'password' => Hash::make('OldPassword123!'),
    ]);

    $response = $this
        ->withHeaders(usersAuthHeader())
        ->putJson('/api/v1/users/' . $user->id, [
            'name' => 'Updated User',
            'username' => 'updateduser',
            'email' => 'updated@example.com',
            'role' => 'admin',
            'password' => 'NewPassword123!',
            'email_verified_at' => true,
        ]);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'User updated successfully.')
        ->assertJsonPath('data.name', 'Updated User')
        ->assertJsonPath('data.username', 'updateduser')
        ->assertJsonPath('data.email', 'updated@example.com')
        ->assertJsonPath('data.role', 'admin');

    $freshUser = $user->fresh();

    expect($freshUser->email_verified_at)->not->toBeNull();
    expect(Hash::check('NewPassword123!', $freshUser->password))->toBeTrue();
});

it('rejects changing the role of admin and superadmin users', function () {
    $user = User::factory()->create([
        'username' => 'admin',
        'role' => 'admin',
    ]);

    $response = $this
        ->withHeaders(usersAuthHeader())
        ->putJson('/api/v1/users/' . $user->id, [
            'name' => $user->name,
            'username' => 'admin',
            'email' => $user->email,
            'role' => 'user',
        ]);

    $response
        ->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Forbidden: Role cannot be changed.');
});

it('rejects changing admin verification status to unverified', function () {
    $user = User::factory()->create([
        'username' => 'admin',
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);

    $response = $this
        ->withHeaders(usersAuthHeader())
        ->putJson('/api/v1/users/' . $user->id, [
            'name' => $user->name,
            'username' => 'admin',
            'email' => $user->email,
            'role' => 'admin',
            'email_verified_at' => false,
        ]);

    $response
        ->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Forbidden: Email verification status cannot be changed.');
});

it('returns not found when updating a missing user', function () {
    $missingUserId = (string) Str::uuid();

    $response = $this
        ->withHeaders(usersAuthHeader())
        ->putJson('/api/v1/users/' . $missingUserId, [
            'name' => 'Missing User',
        ]);

    $response
        ->assertNotFound()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'User not found.');
});

it('soft deletes a regular user', function () {
    $user = User::factory()->create();

    $response = $this
        ->withHeaders(usersAuthHeader())
        ->deleteJson('/api/v1/users/' . $user->id);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'User deleted successfully.');

    $this->assertSoftDeleted('users', [
        'id' => $user->id,
    ]);
});

it('rejects deleting admin and superadmin users', function () {
    $user = User::factory()->create([
        'username' => 'superadmin',
        'role' => 'superadmin',
    ]);

    $response = $this
        ->withHeaders(usersAuthHeader())
        ->deleteJson('/api/v1/users/' . $user->id);

    $response
        ->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Forbidden: Cannot delete admin or superadmin users.');
});

it('restores a soft deleted user', function () {
    $user = User::factory()->create();
    $user->delete();

    $response = $this
        ->withHeaders(usersAuthHeader())
        ->postJson('/api/v1/users/' . $user->id . '/restore');

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'User restored successfully.')
        ->assertJsonPath('data.id', $user->id);

    expect($user->fresh()->trashed())->toBeFalse();
});

it('rejects unauthenticated access to users endpoints', function () {
    $response = $this->getJson('/api/v1/users');

    $response->assertUnauthorized();
});

it('rejects non-admin user from accessing users endpoint', function () {
    $user = User::factory()->create(['role' => 'user']);
    $token = $user->createToken('authToken', TokenAbility::abilitiesForRole('user'))->plainTextToken;

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $token])
        ->getJson('/api/v1/users');

    $response
        ->assertForbidden()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Forbidden: You do not have the required permission.');
});

it('allows admin user to access users endpoint', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $token = $admin->createToken('authToken', TokenAbility::abilitiesForRole('admin'))->plainTextToken;

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $token])
        ->getJson('/api/v1/users');

    $response
        ->assertOk()
        ->assertJsonPath('success', true);
});

it('allows superadmin user to access users endpoint', function () {
    $superadmin = User::factory()->create(['role' => 'superadmin']);
    $token = $superadmin->createToken('authToken', TokenAbility::abilitiesForRole('superadmin'))->plainTextToken;

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer ' . $token])
        ->getJson('/api/v1/users');

    $response
        ->assertOk()
        ->assertJsonPath('success', true);
});
