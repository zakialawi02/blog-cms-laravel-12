<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\PersonalAccessToken;

it('registers a user through the api auth endpoint', function () {
    $response = $this->postJson('/api/auth/register', [
        'name' => 'Auth User',
        'username' => 'authuser',
        'email' => 'auth@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Registered successfully and logged in')
        ->assertJsonPath('data.email', 'auth@example.com')
        ->assertJsonStructure([
            'success',
            'message',
            'token',
            'token_type',
            'data' => ['id', 'name', 'username', 'email', 'role'],
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'auth@example.com',
        'username' => 'authuser',
        'role' => 'user',
    ]);

    expect(PersonalAccessToken::count())->toBe(1);
});

it('logs in a user with email', function () {
    $user = User::factory()->create([
        'email' => 'auth@example.com',
        'password' => Hash::make('Password123!'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'id_user' => $user->email,
        'password' => 'Password123!',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Logged in')
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.email', $user->email);

    expect(PersonalAccessToken::count())->toBe(1);
});

it('logs in a user with username', function () {
    $user = User::factory()->create([
        'username' => 'authuser',
        'password' => Hash::make('Password123!'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'id_user' => $user->username,
        'password' => 'Password123!',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.username', 'authuser');

    expect(PersonalAccessToken::count())->toBe(1);
});

it('rejects invalid credentials', function () {
    $user = User::factory()->create([
        'password' => Hash::make('Password123!'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'id_user' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response
        ->assertUnauthorized()
        ->assertJsonPath('success', false);
});

it('logs out the authenticated api user and deletes the current token', function () {
    $user = User::factory()->create();
    $plainTextToken = $user->createToken('authToken')->plainTextToken;
    $tokenId = PersonalAccessToken::firstOrFail()->id;

    $response = $this
        ->withHeader('Authorization', 'Bearer '.$plainTextToken)
        ->postJson('/api/auth/logout');

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Logged out');

    $this->assertDatabaseMissing('personal_access_tokens', [
        'id' => $tokenId,
    ]);
});

it('sends a password reset link for a known email', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'reset@example.com',
    ]);

    $response = $this->postJson('/api/auth/forgot-password', [
        'email' => $user->email,
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Password reset link sent successfully');

    $this->assertDatabaseHas('password_reset_tokens', [
        'email' => $user->email,
    ]);

    Notification::assertSentTo($user, ResetPassword::class);
});

it('resets the password with a valid token', function () {
    $user = User::factory()->create([
        'email' => 'reset@example.com',
        'password' => Hash::make('OldPassword123!'),
    ]);

    $token = Password::broker()->createToken($user);

    $response = $this->postJson('/api/auth/reset-password', [
        'email' => $user->email,
        'token' => $token,
        'password' => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('success', true);

    expect(Hash::check('NewPassword123!', $user->fresh()->password))->toBeTrue();
});

it('sends an email verification notification for an unverified authenticated user', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create();
    $plainTextToken = $user->createToken('authToken')->plainTextToken;

    $response = $this
        ->withHeader('Authorization', 'Bearer '.$plainTextToken)
        ->postJson('/api/auth/email/verification-notification');

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Verification link sent');

    Notification::assertSentTo($user, VerifyEmail::class);
});

it('verifies the authenticated user email with a signed url', function () {
    $user = User::factory()->unverified()->create();
    $plainTextToken = $user->createToken('authToken')->plainTextToken;

    $verificationUrl = URL::temporarySignedRoute(
        'api.auth.verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]
    );

    $response = $this
        ->withHeader('Authorization', 'Bearer '.$plainTextToken)
        ->getJson($verificationUrl);

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Email verified');

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});
