<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_CALLBACK_URL'),
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_CALLBACK_URL'),
    ],
    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_CALLBACK_URL'),
    ],
    'twitter' => [
        'client_id' => env('TWITTER_CLIENT_ID'),
        'client_secret' => env('TWITTER_CLIENT_SECRET'),
        'redirect' => env('TWITTER_CALLBACK_URL'),
    ],
    'linkedin' => [
        'client_id' => env('LINKEDIN_CLIENT_ID'),
        'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
        'redirect' => env('LINKEDIN_CALLBACK_URL'),
    ],
    'microsoft' => [
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'redirect' => env('MICROSOFT_CALLBACK_URL'),
    ],
    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
    ],
    'sumopod' => [
        'key' => env('SUMOPOD_API_KEY'),
    ],
    'qwen' => [
        'key' => env('DASHSCOPE_API_KEY'),
    ],
    'cloudflare' => [
        'token' => env('CLOUDFLARE_AI_API_TOKEN'),
        'account_id' => env('CLOUDFLARE_ACCOUNT_ID'),
    ],
    'ipinfo' => [
        'token' => env('IPINFO_ACCESS_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google reCAPTCHA
    |--------------------------------------------------------------------------
    |
    | Melindungi form register (web & API) dari bot. Verifikasi dilakukan
    | server-side ke endpoint siteverify milik Google.
    |
    | Catatan: rule hanya AKTIF kalau 'secret_key' terisi. Jadi selama secret
    | belum dipasang, register berjalan normal (tidak ada risiko lockout).
    |
    */

    'recaptcha' => [
        // 'v2' = checkbox "I'm not a robot", 'v3' = invisible (score-based)
        'version' => env('RECAPTCHA_VERSION', 'v2'),
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
        // Kill switch global
        'enabled' => env('RECAPTCHA_ENABLED', true),
        // API register: default OFF (API sudah dijaga X-API-Key + Origin)
        'enabled_for_api' => env('RECAPTCHA_ENABLED_FOR_API', false),
        'verify_url' => 'https://www.google.com/recaptcha/api/siteverify',
        // v3 only
        'min_score' => env('RECAPTCHA_MIN_SCORE', 0.5),
        // false = fail-closed (tolak kalau Google tidak bisa dihubungi)
        'fail_open' => env('RECAPTCHA_FAIL_OPEN', false),
        'timeout' => env('RECAPTCHA_TIMEOUT', 5),
    ],
];
