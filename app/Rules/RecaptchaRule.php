<?php

namespace App\Rules;

use Closure;
use Throwable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class RecaptchaRule
 *
 * Memverifikasi token Google reCAPTCHA (v2 checkbox atau v3) ke endpoint
 * siteverify milik Google. Dipakai bersama oleh jalur register web
 * (RegisterRequest) dan API (Api\Auth\RegisterController).
 *
 * Rule ini AKTIF hanya kalau `services.recaptcha.secret_key` terisi dan
 * `enabled` bernilai true. Kalau belum dikonfigurasi, rule langsung lolos
 * sehingga register tetap berjalan normal (tidak ada risiko lockout).
 *
 * @package App\Rules
 */
class RecaptchaRule implements ValidationRule
{
    /**
     * Jalankan rule ini walaupun nilainya string kosong / field tidak ada.
     *
     * Laravel melewatkan rule non-implicit saat nilai field kosong
     * (Validator::presentOrRuleIsImplicit). Karena kita justru perlu menolak
     * token yang kosong saat reCAPTCHA aktif, rule ini harus bersifat implicit.
     * Mekanismenya: InvokableValidationRule::make() membaca properti ini.
     *
     * @var bool
     */
    public $implicit = true;

    /**
     * @param  bool  $forApi  True bila dipakai di jalur API (butuh opt-in
     *                        terpisah via RECAPTCHA_ENABLED_FOR_API).
     */
    public function __construct(
        protected bool $forApi = false,
    ) {
    }

    /**
     * Validate the given attribute.
     *
     * @param  string   $attribute  The attribute name being validated.
     * @param  mixed    $value      The attribute value (token reCAPTCHA).
     * @param  \Closure $fail       The fail callback to invoke if validation fails.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $config = (array) config('services.recaptcha', []);

        if (! $this->isActive($config)) {
            return;
        }

        $token = is_string($value) ? trim($value) : '';

        if ($token === '') {
            $fail($this->missingTokenMessage($config));
            return;
        }

        try {
            $response = Http::asForm()
                ->timeout((int) ($config['timeout'] ?? 5))
                ->post($config['verify_url'] ?? 'https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $config['secret_key'] ?? '',
                    'response' => $token,
                    'remoteip' => request()->ip(),
                ]);

            $body = (array) ($response->json() ?? []);
            $errorCodes = (array) ($body['error-codes'] ?? []);

            if ($response->failed() || ! ($body['success'] ?? false)) {
                Log::warning('reCAPTCHA verification failed', [
                    'attribute' => $attribute,
                    'error_codes' => $errorCodes,
                    'ip' => request()->ip(),
                ]);

                $fail($this->failureMessage($errorCodes, $config));
                return;
            }

            // v3: tolak skor di bawah ambang batas.
            if (($config['version'] ?? 'v2') === 'v3') {
                $minScore = (float) ($config['min_score'] ?? 0.5);
                $score = (float) ($body['score'] ?? 0);

                if ($score < $minScore) {
                    Log::info('reCAPTCHA score below threshold', [
                        'score' => $score,
                        'min_score' => $minScore,
                        'ip' => request()->ip(),
                    ]);

                    $fail(__('Security verification failed. Please try again.'));
                    return;
                }

                // Skor diterima — dicatat untuk membantu tuning ambang batas.
                Log::debug('reCAPTCHA v3 score accepted', [
                    'score' => $score,
                    'min_score' => $minScore,
                    'ip' => request()->ip(),
                ]);
            }
        } catch (Throwable $e) {
            // Google tidak bisa dihubungi / timeout.
            Log::error('reCAPTCHA verification error', [
                'message' => $e->getMessage(),
            ]);

            if (! ($config['fail_open'] ?? false)) {
                $fail(__('Security verification is unavailable right now. Please try again later.'));
            }
        }
    }

    /**
     * Apakah verifikasi perlu dijalankan?
     *
     * @param  array<string, mixed>  $config
     */
    protected function isActive(array $config): bool
    {
        if (! ($config['enabled'] ?? false)) {
            return false;
        }

        if ($this->forApi && ! ($config['enabled_for_api'] ?? false)) {
            return false;
        }

        // Sumber kebenaran tunggal: tanpa secret, verifikasi tidak mungkin
        // dilakukan — jangan blokir register.
        return filled($config['secret_key'] ?? null);
    }

    /**
     * Pesan error yang ramah berdasarkan kode error dari Google.
     *
     * @param  array<int, string>   $errorCodes
     * @param  array<string, mixed> $config
     */
    protected function failureMessage(array $errorCodes, array $config = []): string
    {
        if (in_array('timeout-or-duplicate', $errorCodes, true)) {
            return ($config['version'] ?? 'v2') === 'v3'
                ? __('Verification expired. Please submit the form again.')
                : __('Verification expired. Please tick the box again.');
        }

        return __('Security verification failed. Please try again.');
    }

    /**
     * Pesan untuk token yang kosong.
     *
     * Pada v2 token kosong berarti pengguna belum mencentang kotak.
     * Pada v3 tidak ada kotak untuk dicentang — token kosong praktis selalu
     * berarti script Google gagal dimuat (JS diblokir / adblocker), jadi
     * pesannya diarahkan ke memuat ulang halaman.
     *
     * @param  array<string, mixed>  $config
     */
    protected function missingTokenMessage(array $config): string
    {
        if (($config['version'] ?? 'v2') === 'v3') {
            return __('Security verification could not be completed. Please reload the page and try again.');
        }

        return __('Please tick "I\'m not a robot" to continue.');
    }
}
