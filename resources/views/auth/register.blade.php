@section('title', $data['title'] ?? 'Register')
@section('meta_description', '')

<x-guest-layout>
    <h1 class="text-center text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
        {{ __('Create your Account') }}
    </h1>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('error'))
        <div class="text-error text-sm font-medium" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if ((env('GOOGLE_CLIENT_ID') && env('GOOGLE_CLIENT_SECRET')) || (env('GITHUB_CLIENT_ID') && env('GITHUB_CLIENT_SECRET')) || (env('FACEBOOK_CLIENT_ID') && env('FACEBOOK_CLIENT_SECRET')) || (env('MICROSOFT_CLIENT_ID') && env('MICROSOFT_CLIENT_SECRET')) || (env('LINKEDIN_CLIENT_ID') && env('LINKEDIN_CLIENT_SECRET')) || (env('TWITTER_CLIENT_ID') && env('TWITTER_CLIENT_SECRET')))
        <div class="mb-4 text-center">
            <p class="mb-3 text-sm text-gray-600 dark:text-gray-400">{{ __('Sign Up with your social account') }}</p>
        </div>

        <div class="flex justify-center">
            <div class="flex flex-wrap justify-center gap-3">
                @if (env('GOOGLE_CLIENT_ID') && env('GOOGLE_CLIENT_SECRET'))
                    <a class="bg-background text-foreground flex items-center justify-center rounded-lg border p-3 transition-colors duration-200 hover:opacity-70" href="{{ route('auth.redirect', ['provider' => 'google'] + (request()->has('redirect') ? ['redirect' => request()->get('redirect')] : [])) }}">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" />
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                        </svg>
                    </a>
                @endif

                @if (env('FACEBOOK_CLIENT_ID') && env('FACEBOOK_CLIENT_SECRET'))
                    <a class="bg-background text-foreground flex items-center justify-center rounded-lg border p-3 transition-colors duration-200 hover:opacity-70" href="{{ route('auth.redirect', ['provider' => 'facebook'] + (request()->has('redirect') ? ['redirect' => request()->get('redirect')] : [])) }}">
                        <svg class="h-5 w-5" fill="#1877F2" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                    </a>
                @endif

                @if (env('MICROSOFT_CLIENT_ID') && env('MICROSOFT_CLIENT_SECRET'))
                    <a class="bg-background text-foreground flex items-center justify-center rounded-lg border p-3 transition-colors duration-200 hover:opacity-70" href="{{ route('auth.redirect', ['provider' => 'microsoft'] + (request()->has('redirect') ? ['redirect' => request()->get('redirect')] : [])) }}">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                            <path d="M11.4 24H0V12.6h11.4V24zM24 24H12.6V12.6H24V24zM11.4 11.4H0V0h11.4v11.4zM24 11.4H12.6V0H24v11.4z" fill="#00A1F1" />
                        </svg>
                    </a>
                @endif

                @if (env('GITHUB_CLIENT_ID') && env('GITHUB_CLIENT_SECRET'))
                    <a class="bg-background text-foreground flex items-center justify-center rounded-lg border p-3 transition-colors duration-200 hover:opacity-70" href="{{ route('auth.redirect', ['provider' => 'github'] + (request()->has('redirect') ? ['redirect' => request()->get('redirect')] : [])) }}">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0C5.374 0 0 5.373 0 12 0 17.302 3.438 21.8 8.207 23.387c.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z" />
                        </svg>
                    </a>
                @endif

                @if (env('LINKEDIN_CLIENT_ID') && env('LINKEDIN_CLIENT_SECRET'))
                    <a class="bg-background text-foreground flex items-center justify-center rounded-lg border p-3 transition-colors duration-200 hover:opacity-70" href="{{ route('auth.redirect', ['provider' => 'linkedin'] + (request()->has('redirect') ? ['redirect' => request()->get('redirect')] : [])) }}">
                        <svg class="h-5 w-5" fill="#0077B5" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                        </svg>
                    </a>
                @endif

                @if (env('TWITTER_CLIENT_ID') && env('TWITTER_CLIENT_SECRET'))
                    <a class="bg-background text-foreground flex items-center justify-center rounded-lg border p-3 transition-colors duration-200 hover:opacity-70" href="{{ route('auth.redirect', ['provider' => 'twitter'] + (request()->has('redirect') ? ['redirect' => request()->get('redirect')] : [])) }}">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                        </svg>
                    </a>
                @endif
            </div>
        </div>

        <div class="my-4 mb-3 flex items-center text-xs uppercase text-gray-400 before:me-6 before:flex-1 before:border-t before:border-gray-200 after:ms-6 after:flex-1 after:border-t after:border-gray-200 dark:text-neutral-500 dark:before:border-neutral-600 dark:after:border-neutral-600">{{ __('or') }}</div>
    @endif

    <form class="space-y-3 md:space-y-4" method="POST" action="{{ route('register') }}">
        @csrf

        <input class="d-none" name="_code" type="hidden" value="" tabindex="-1" autocomplete="off">

        <!-- Name -->
        <div>
            <x-dashboard.input-label for="name" :value="__('Name')" />
            <x-dashboard.text-input class="mt-1 block w-full" id="name" name="name" type="text" :value="old('name')" required autofocus autocomplete="name" placeholder="John Doe" />
            <x-dashboard.input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- userName -->
        <div>
            <x-dashboard.input-label for="username" :value="__('Username')" />
            <x-dashboard.text-input class="mt-1 block w-full" id="username" name="username" type="text" :value="old('username')" required autofocus autocomplete="username" placeholder="John Doe" />
            <x-dashboard.input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-dashboard.input-label for="email" :value="__('Email')" />
            <x-dashboard.text-input class="mt-1 block w-full" id="email" name="email" type="email" :value="old('email')" required autocomplete="username" placeholder="name@mail.com" />
            <x-dashboard.input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-dashboard.input-label for="password" :value="__('Password')" />

            <x-dashboard.text-input class="mt-1 block w-full" id="password" name="password" type="password" required autocomplete="new-password" placeholder="**********" />

            <x-dashboard.input-error class="mt-2" :messages="$errors->get('password')" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-dashboard.input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-dashboard.text-input class="mt-1 block w-full" id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="**********" />

            <x-dashboard.input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
        </div>

        <x-dashboard.primary-button class="w-full">
            {{ __('Register') }}
        </x-dashboard.primary-button>

        <a class="text-back-muted dark:text-back-light hover:dark:text-back-light/70 text-sm underline" href="{{ route('login') }}">
            {{ __('Already registered?') }}
        </a>

    </form>
</x-guest-layout>
