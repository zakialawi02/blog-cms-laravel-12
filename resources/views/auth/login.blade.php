@section('title', $data['title'] ?? 'Login')
@section('meta_description', '')

<x-guest-layout>
    <h1 class="text-center text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
        {{ __('Sign in to your account') }}
    </h1>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (env('GOOGLE_CLIENT_ID') && env('GOOGLE_CLIENT_SECRET') ?? false)
        <button class="shadow-2xs focus:outline-hidden dark:border-back-neutral-700 dark:bg-back-neutral-900 dark:hover:bg-back-neutral-800 dark:focus:bg-back-neutral-800 inline-flex w-full items-center justify-center gap-x-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-50 focus:bg-gray-50 disabled:pointer-events-none disabled:opacity-50" type="button">
            <svg class="h-auto w-4" width="46" height="47" viewBox="0 0 46 47" fill="none">
                <path d="M46 24.0287C46 22.09 45.8533 20.68 45.5013 19.2112H23.4694V27.9356H36.4069C36.1429 30.1094 34.7347 33.37 31.5957 35.5731L31.5663 35.8669L38.5191 41.2719L38.9885 41.3306C43.4477 37.2181 46 31.1669 46 24.0287Z" fill="#4285F4" />
                <path d="M23.4694 47C29.8061 47 35.1161 44.9144 39.0179 41.3012L31.625 35.5437C29.6301 36.9244 26.9898 37.8937 23.4987 37.8937C17.2793 37.8937 12.0281 33.7812 10.1505 28.1412L9.88649 28.1706L2.61097 33.7812L2.52296 34.0456C6.36608 41.7125 14.287 47 23.4694 47Z" fill="#34A853" />
                <path d="M10.1212 28.1413C9.62245 26.6725 9.32908 25.1156 9.32908 23.5C9.32908 21.8844 9.62245 20.3275 10.0918 18.8588V18.5356L2.75765 12.8369L2.52296 12.9544C0.909439 16.1269 0 19.7106 0 23.5C0 27.2894 0.909439 30.8731 2.49362 34.0456L10.1212 28.1413Z" fill="#FBBC05" />
                <path d="M23.4694 9.07688C27.8699 9.07688 30.8622 10.9863 32.5344 12.5725L39.1645 6.11C35.0867 2.32063 29.8061 0 23.4694 0C14.287 0 6.36607 5.2875 2.49362 12.9544L10.0918 18.8588C11.9987 13.1894 17.25 9.07688 23.4694 9.07688Z" fill="#EB4335" />
            </svg>
            {{ __('Sign in with Google') }}
        </button>
    @endif

    @if (env('GOOGLE_CLIENT_ID') && env('GOOGLE_CLIENT_SECRET') ?? false)
        <div class="dark:text-back-neutral-500 dark:before:border-back-neutral-600 dark:after:border-back-neutral-600 flex items-center text-xs uppercase text-gray-400 before:me-6 before:flex-1 before:border-t before:border-gray-200 after:ms-6 after:flex-1 after:border-t after:border-gray-200">Or</div>
    @endif

    <form class="space-y-3 md:space-y-4" method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-dashboard.input-label for="id_user" :value="__('Username/Email')" />
            <x-dashboard.text-input class="mt-1 block w-full" id="id_user" name="id_user" type="text" :value="old('id_user')" required autofocus autocomplete="username" placeholder="name@mail.com" />
            <x-dashboard.input-error class="mt-2" :messages="$errors->get('id_user')" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-dashboard.input-label for="password" :value="__('Password')" />

            <x-dashboard.text-input class="mt-1 block w-full" id="password" name="password" type="password" required autocomplete="current-password" placeholder="**********" />

            <x-dashboard.input-error class="mt-2" :messages="$errors->get('password')" />
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-start">
                <!-- Remember Me -->
                <div class="mt-2">
                    <label class="inline-flex items-center" for="remember_me">
                        <input class="shadow-xs text-back-dark focus:ring-back-dark dark:focus:ring-back-light dark:text-back-secondary rounded-sm" id="remember_me" name="remember" type="checkbox">
                        <span class="text-back-dark dark:text-back-light ms-2 text-sm">{{ __('Remember me') }}</span>
                    </label>
                </div>
            </div>
            @if (Route::has('password.request'))
                <a class="text-back-muted dark:text-back-light/70 text-sm font-medium hover:underline" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <x-dashboard.primary-button class="w-full">
            {{ __('Log in') }}
        </x-dashboard.primary-button>

        <p class="text-back-dark dark:text-back-light font-back-light text-sm">
            {{ __('Don’t have an account yet?') }} <a class="text-back-muted dark:text-back-light/70 font-medium hover:underline" href={{ route('register') }}> {{ __('Sign up') }}</a>
        </p>
    </form>
</x-guest-layout>
