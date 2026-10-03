<?php

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * Helper: payload register web.
 *
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function registerPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Recaptcha User',
        'username' => 'recaptchauser',
        'email' => 'recaptcha@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        '_code' => '',
    ], $overrides);
}

it('registers normally when recaptcha is not configured', function () {
    Http::preventStrayRequests();

    $this->post('/register', registerPayload())->assertRedirect();

    $this->assertDatabaseHas('users', ['email' => 'recaptcha@example.com']);
    Http::assertNothingSent();
});

it('rejects registration when the token is missing and recaptcha is active', function () {
    fakeRecaptchaConfig();
    Http::preventStrayRequests();

    $this->post('/register', registerPayload())
        ->assertSessionHasErrors('g-recaptcha-response');

    $this->assertDatabaseMissing('users', ['email' => 'recaptcha@example.com']);
    Http::assertNothingSent();
});

it('accepts registration when google verifies the token', function () {
    fakeRecaptchaConfig();
    Http::fake([
        'www.google.com/recaptcha/*' => Http::response(['success' => true], 200),
    ]);

    $this->post('/register', registerPayload(['g-recaptcha-response' => 'valid-token']))
        ->assertRedirect();

    $this->assertDatabaseHas('users', ['email' => 'recaptcha@example.com']);
    Http::assertSentCount(1);
});

it('rejects registration when google refuses the token', function () {
    fakeRecaptchaConfig();
    Http::fake([
        'www.google.com/recaptcha/*' => Http::response([
            'success' => false,
            'error-codes' => ['invalid-input-response'],
        ], 200),
    ]);

    $this->post('/register', registerPayload(['g-recaptcha-response' => 'bad-token']))
        ->assertSessionHasErrors('g-recaptcha-response');

    $this->assertDatabaseMissing('users', ['email' => 'recaptcha@example.com']);
});

it('rejects registration when google is unreachable (fail-closed)', function () {
    fakeRecaptchaConfig(['fail_open' => false]);
    Http::fake(fn () => throw new ConnectionException('Connection timed out'));

    $this->post('/register', registerPayload(['g-recaptcha-response' => 'any-token']))
        ->assertSessionHasErrors('g-recaptcha-response');

    $this->assertDatabaseMissing('users', ['email' => 'recaptcha@example.com']);
});

it('accepts registration when fail_open is enabled and google is unreachable', function () {
    fakeRecaptchaConfig(['fail_open' => true]);
    Http::fake(fn () => throw new ConnectionException('Connection timed out'));

    $this->post('/register', registerPayload(['g-recaptcha-response' => 'any-token']))
        ->assertRedirect();

    $this->assertDatabaseHas('users', ['email' => 'recaptcha@example.com']);
});

it('still blocks bots that fill the honeypot field', function () {
    Http::preventStrayRequests();

    $this->post('/register', registerPayload(['_code' => 'i-am-a-bot']))
        ->assertRedirect()
        ->assertSessionHas('error', 'Something went wrong.');

    $this->assertDatabaseMissing('users', ['email' => 'recaptcha@example.com']);
});

it('still enforces the rate limit check', function () {
    Http::preventStrayRequests();

    $throttleKey = Str::transliterate(Str::lower('recaptcha@example.com') . '|127.0.0.1');

    for ($i = 0; $i < 5; $i++) {
        RateLimiter::hit($throttleKey, 300);
    }

    $this->post('/register', registerPayload())
        ->assertSessionHasErrors('email');

    $this->assertDatabaseMissing('users', ['email' => 'recaptcha@example.com']);
});

it('renders the recaptcha widget on the register page when active', function () {
    fakeRecaptchaConfig();

    $this->get('/register')
        ->assertOk()
        ->assertSee('g-recaptcha', escape: false)
        ->assertSee('test-site-key', escape: false)
        ->assertSee('recaptcha/api.js', escape: false);
});

it('does not render the recaptcha widget when not configured', function () {
    fakeRecaptchaConfig(['secret_key' => null]);

    $this->get('/register')
        ->assertOk()
        ->assertDontSee('g-recaptcha', escape: false)
        ->assertDontSee('recaptcha/api.js', escape: false);
});
