<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;

function authHeaderForUser(User $user): array
{
    $token = $user->createToken('authToken')->plainTextToken;

    return ['Authorization' => 'Bearer '.$token];
}

it('returns the authenticated user profile', function () {
    $user = User::factory()->create();

    $response = $this
        ->withHeaders(authHeaderForUser($user))
        ->getJson('/api/v1/user/me');

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'User details/My profile')
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.email', $user->email);
});

it('updates the authenticated user profile and resets email verification when email changes', function () {
    $user = User::factory()->create([
        'email' => 'old@example.com',
        'email_verified_at' => now(),
    ]);

    $response = $this
        ->withHeaders(authHeaderForUser($user))
        ->patchJson('/api/v1/user', [
            'name' => 'Updated Name',
            'email' => 'new@example.com',
        ]);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Profile updated successfully.')
        ->assertJsonPath('data.name', 'Updated Name')
        ->assertJsonPath('data.email', 'new@example.com')
        ->assertJsonPath('data.email_verified_at', null);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'new@example.com',
    ]);

    expect($user->fresh()->email_verified_at)->toBeNull();
});

it('updates the authenticated user password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('OldPassword123!'),
    ]);

    $response = $this
        ->withHeaders(authHeaderForUser($user))
        ->patchJson('/api/v1/user/password', [
            'current_password' => 'OldPassword123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Password updated successfully.');

    expect(Hash::check('NewPassword123!', $user->fresh()->password))->toBeTrue();
});

it('rejects password update when current password is wrong', function () {
    $user = User::factory()->create([
        'password' => Hash::make('OldPassword123!'),
    ]);

    $response = $this
        ->withHeaders(authHeaderForUser($user))
        ->patchJson('/api/v1/user/password', [
            'current_password' => 'wrong-password',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

    $response
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Validation error.');
});

it('updates the authenticated user profile photo', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $response = $this
        ->withHeaders(authHeaderForUser($user))
        ->patch('/api/v1/user/photo', [
            'photo_profile' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

    $response
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Profile photo updated successfully.');

    expect($user->fresh()->profile_photo_path)->toStartWith('/storage/profile_photos/');

    Storage::disk('public')->assertExists(str_replace('/storage/', '', $user->fresh()->profile_photo_path));
});

it('deletes the authenticated user account and current token', function () {
    $user = User::factory()->create([
        'password' => Hash::make('Password123!'),
    ]);

    $plainTextToken = $user->createToken('authToken')->plainTextToken;
    $tokenId = PersonalAccessToken::firstOrFail()->id;

    $response = $this
        ->withHeader('Authorization', 'Bearer '.$plainTextToken)
        ->deleteJson('/api/v1/user', [
            'password' => 'Password123!',
        ]);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'User deleted successfully.');

    $this->assertSoftDeleted('users', [
        'id' => $user->id,
    ]);

    $this->assertDatabaseMissing('personal_access_tokens', [
        'id' => $tokenId,
    ]);
});

it('rejects account deletion when password does not match', function () {
    $user = User::factory()->create([
        'password' => Hash::make('Password123!'),
    ]);

    $response = $this
        ->withHeaders(authHeaderForUser($user))
        ->deleteJson('/api/v1/user', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertUnprocessable()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Password does not match.');
});
