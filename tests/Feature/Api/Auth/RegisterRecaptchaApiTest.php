<?php

use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * Helper: payload register API.
 *
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function apiRegisterPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Api Recaptcha User',
        'username' => 'apirecaptcha',
        'email' => 'api-recaptcha@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ], $overrides);
}

it('registers through the api without a token when recaptcha is not configured', function () {
    Http::preventStrayRequests();

    $this->postJson('/api/auth/register', apiRegisterPayload())
        ->assertCreated()
        ->assertJsonPath('success', true);

    $this->assertDatabaseHas('users', ['email' => 'api-recaptcha@example.com']);
    Http::assertNothingSent();
});

it('ignores recaptcha on the api by default even when configured', function () {
    // enabled_for_api default false -> API tidak boleh berubah perilakunya.
    fakeRecaptchaConfig(['enabled_for_api' => false]);
    Http::preventStrayRequests();

    $this->postJson('/api/auth/register', apiRegisterPayload())
        ->assertCreated();

    Http::assertNothingSent();
});

it('requires a valid token on the api when enabled_for_api is true', function () {
    fakeRecaptchaConfig(['enabled_for_api' => true]);
    Http::preventStrayRequests();

    $this->postJson('/api/auth/register', apiRegisterPayload())
        ->assertStatus(422)
        ->assertJsonPath('success', false)
        ->assertJsonValidationErrors('recaptcha_token');

    $this->assertDatabaseMissing('users', ['email' => 'api-recaptcha@example.com']);
    Http::assertNothingSent();
});

it('accepts api registration when google verifies the token', function () {
    fakeRecaptchaConfig(['enabled_for_api' => true]);
    Http::fake([
        'www.google.com/recaptcha/*' => Http::response(['success' => true], 200),
    ]);

    $this->postJson('/api/auth/register', apiRegisterPayload([
        'recaptcha_token' => 'valid-api-token',
    ]))
        ->assertCreated()
        ->assertJsonPath('success', true);

    $this->assertDatabaseHas('users', ['email' => 'api-recaptcha@example.com']);
    expect(User::where('email', 'api-recaptcha@example.com')->exists())->toBeTrue();
    Http::assertSentCount(1);
});

it('rejects api registration when google refuses the token', function () {
    fakeRecaptchaConfig(['enabled_for_api' => true]);
    Http::fake([
        'www.google.com/recaptcha/*' => Http::response([
            'success' => false,
            'error-codes' => ['invalid-input-response'],
        ], 200),
    ]);

    $this->postJson('/api/auth/register', apiRegisterPayload([
        'recaptcha_token' => 'bad-api-token',
    ]))
        ->assertStatus(422)
        ->assertJsonValidationErrors('recaptcha_token');

    $this->assertDatabaseMissing('users', ['email' => 'api-recaptcha@example.com']);
});

it('rejects api registration when google is unreachable and fail_open is false', function () {
    fakeRecaptchaConfig(['enabled_for_api' => true, 'fail_open' => false]);
    Http::fake(fn () => throw new ConnectionException('Connection timed out'));

    $this->postJson('/api/auth/register', apiRegisterPayload([
        'recaptcha_token' => 'any-token',
    ]))
        ->assertStatus(422)
        ->assertJsonValidationErrors('recaptcha_token');
});

it('throttles api registration after five attempts from the same ip', function () {
    Http::preventStrayRequests();

    for ($i = 1; $i <= 5; $i++) {
        $this->postJson('/api/auth/register', apiRegisterPayload([
            'username' => "apiuser{$i}",
            'email' => "api-user{$i}@example.com",
        ]))->assertCreated();
    }

    $this->postJson('/api/auth/register', apiRegisterPayload([
        'username' => 'apiuser6',
        'email' => 'api-user6@example.com',
    ]))
        ->assertStatus(429)
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Too many registration attempts. Please try again later.');
});
