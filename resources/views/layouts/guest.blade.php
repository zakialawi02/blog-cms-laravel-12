<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script>
            (function() {
                if (localStorage.getItem("theme") === "dark") {
                    document.documentElement.classList.add("dark");
                }
            })();
        </script>
        <title>@yield('title') - {{ $data['web_setting']['web_name'] ?? config('app.name') }}</title>

        <meta name="description" content="@yield('meta_description', 'Securely access your account. Log in or sign up to manage your profile, settings, and preferences.')">
        <meta name="author" content="@yield('meta_author', 'Zaki Alawi')">
        <meta name="keywords" content="@yield('meta_keywords', 'login, sign in, register, authentication, user account, secure access')">
        <meta property="og:title" content="@yield('og_title', 'Authentication Pages | ' . config('app.name'))" />
        <meta property="og:type" content="@yield('og_type', 'website')" />
        <meta property="og:url" content="@yield('og_url', url()->current())" />
        <meta property="og:description" content="@yield('og_description', 'Authentication Pages | ' . config('app.name'))" />
        <meta property="og:image" content="@yield('og_image', asset('assets/img/favicon.png'))" />

        <meta name="robots" content="@yield('meta_robots', 'index,follow')">

        <link type="image/png" href="{{ asset('assets/app_logo/' . ($data['web_setting']['favicon'] ?? 'favicon.png')) }}" rel="icon">

        <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />

        @stack('css')
        {{ $css ?? '' }}

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @if (filled($data['web_setting']['before_close_head'] ?? false))
            {!! $data['web_setting']['before_close_head'] ?? null !!}
        @endif
    </head>

    <body class="font-sans antialiased">
        <section class="bg-back-base-200 dark:bg-back-dark-base-200 min-h-screen">
            <div class="mx-auto flex flex-col items-center justify-center px-6 py-8 md:min-h-screen lg:py-4">
                <div class="mb-4 flex items-center text-xl font-semibold text-gray-900 dark:text-white">
                    <a href="/">
                        <x-application-logo class="h-auto max-h-12 w-24 fill-current text-gray-500" />
                    </a>
                </div>

                <div class="dark:border-back-muted dark:bg-back-dark-base-100 bg-back-base-100 w-full rounded-lg shadow sm:max-w-md md:mt-0 xl:p-0 dark:border">
                    <div class="space-y-4 p-5 sm:p-8 md:space-y-6">

                        {{ $slot }}

                    </div>
                </div>
            </div>
        </section>

        <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
        @stack('javascript')
        {{ $javascript ?? '' }}

        @if (filled($data['web_setting']['before_close_body'] ?? false))
            {!! $data['web_setting']['before_close_body'] ?? null !!}
        @endif
    </body>

</html>
