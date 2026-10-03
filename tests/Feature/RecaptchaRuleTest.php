<?php

use App\Rules\RecaptchaRule;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

/**
 * Helper: pasang konfigurasi reCAPTCHA untuk pengujian.
 *
 * @param  array<string, mixed>  $overrides
 */
function fakeRecaptchaConfig(array $overrides = []): void
{
    config()->set('services.recaptcha', array_merge([
        'version' => 'v2',
        'site_key' => 'test-site-key',
        'secret_key' => 'test-secret-key',
        'enabled' => true,
        'enabled_for_api' => false,
        'verify_url' => 'https://www.google.com/recaptcha/api/siteverify',
        'min_score' => 0.5,
        'fail_open' => false,
        'timeout' => 5,
    ], $overrides));
}

/**
 * Helper: jalankan RecaptchaRule lewat Validator.
 */
function recaptchaValidator(mixed $value, bool $forApi = false): Illuminate\Contracts\Validation\Validator
{
    return Validator::make(
        ['token' => $value],
        ['token' => [new RecaptchaRule($forApi)]]
    );
}

it('bypasses verification when the secret key is not configured', function () {
    fakeRecaptchaConfig(['secret_key' => null]);
    Http::preventStrayRequests();

    expect(recaptchaValidator('')->passes())->toBeTrue();
    Http::assertNothingSent();
});

it('bypasses verification when recaptcha is globally disabled', function () {
    fakeRecaptchaConfig(['enabled' => false]);
    Http::preventStrayRequests();

    expect(recaptchaValidator('')->passes())->toBeTrue();
    Http::assertNothingSent();
});

it('bypasses verification for the api when enabled_for_api is false', function () {
    fakeRecaptchaConfig(['enabled_for_api' => false]);
    Http::preventStrayRequests();

    expect(recaptchaValidator('', forApi: true)->passes())->toBeTrue();
    Http::assertNothingSent();
});

it('fails when the token is empty while verification is active', function () {
    fakeRecaptchaConfig();
    Http::preventStrayRequests();

    $validator = recaptchaValidator('');

    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->first('token'))->toContain('robot');
    Http::assertNothingSent();
});

it('passes when google verifies the token', function () {
    fakeRecaptchaConfig();
    Http::fake([
        'www.google.com/recaptcha/*' => Http::response(['success' => true], 200),
    ]);

    expect(recaptchaValidator('a-valid-token')->passes())->toBeTrue();

    Http::assertSent(function ($request) {
        return $request->url() === 'https://www.google.com/recaptcha/api/siteverify'
            && $request['secret'] === 'test-secret-key'
            && $request['response'] === 'a-valid-token';
    });
});

it('fails when google refuses the token', function () {
    fakeRecaptchaConfig();
    Http::fake([
        'www.google.com/recaptcha/*' => Http::response([
            'success' => false,
            'error-codes' => ['invalid-input-response'],
        ], 200),
    ]);

    $validator = recaptchaValidator('a-bad-token');

    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->first('token'))->toContain('Security verification failed');
});

it('gives a specific message when the token has expired', function () {
    fakeRecaptchaConfig();
    Http::fake([
        'www.google.com/recaptcha/*' => Http::response([
            'success' => false,
            'error-codes' => ['timeout-or-duplicate'],
        ], 200),
    ]);

    expect(recaptchaValidator('expired-token')->errors()->first('token'))
        ->toContain('expired');
});

it('gives a reload hint for v3 when the token is missing', function () {
    // v3 tidak punya checkbox, jadi pesan "tick the box" akan menyesatkan.
    fakeRecaptchaConfig(['version' => 'v3']);
    Http::preventStrayRequests();

    $message = recaptchaValidator('')->errors()->first('token');

    expect($message)->toContain('reload the page');
    expect($message)->not->toContain('robot');
});

it('uses a v3 specific expired-token message', function () {
    fakeRecaptchaConfig(['version' => 'v3']);
    Http::fake([
        'www.google.com/recaptcha/*' => Http::response([
            'success' => false,
            'error-codes' => ['timeout-or-duplicate'],
        ], 200),
    ]);

    expect(recaptchaValidator('expired-token')->errors()->first('token'))
        ->toContain('submit the form again');
});

it('fails closed when google cannot be reached', function () {
    fakeRecaptchaConfig(['fail_open' => false]);
    Http::fake(fn () => throw new ConnectionException('Connection timed out'));

    $validator = recaptchaValidator('any-token');

    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->first('token'))->toContain('unavailable');
});

it('fails open when configured and google cannot be reached', function () {
    fakeRecaptchaConfig(['fail_open' => true]);
    Http::fake(fn () => throw new ConnectionException('Connection timed out'));

    expect(recaptchaValidator('any-token')->passes())->toBeTrue();
});

it('fails for v3 when the score is below the threshold', function () {
    fakeRecaptchaConfig(['version' => 'v3', 'min_score' => 0.5]);
    Http::fake([
        'www.google.com/recaptcha/*' => Http::response(['success' => true, 'score' => 0.1], 200),
    ]);

    expect(recaptchaValidator('low-score-token')->passes())->toBeFalse();
});

it('passes for v3 when the score meets the threshold', function () {
    fakeRecaptchaConfig(['version' => 'v3', 'min_score' => 0.5]);
    Http::fake([
        'www.google.com/recaptcha/*' => Http::response(['success' => true, 'score' => 0.9], 200),
    ]);

    expect(recaptchaValidator('high-score-token')->passes())->toBeTrue();
});

it('fails when google returns an http error', function () {
    fakeRecaptchaConfig();
    Http::fake([
        'www.google.com/recaptcha/*' => Http::response('Bad Gateway', 502),
    ]);

    expect(recaptchaValidator('any-token')->passes())->toBeFalse();
});
