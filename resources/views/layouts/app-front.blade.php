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
        <title>@yield('title') | {{ $data['web_setting']['web_name'] ?? config('app.name') }}</title>

        <meta name="description" content="@yield('meta_description', $data['web_setting']['description'] ?? '')">
        <meta name="author" content="@yield('meta_author', 'Zaki Alawi')">
        <meta name="keywords" content="@yield('meta_keywords', $data['web_setting']['keywords'] ?? 'Zaki Alawi, Blog')">
        <meta property="og:title" content="@yield('og_title', $data['web_setting']['web_name'] ?? config('app.name')) | {{ $data['web_setting']['web_name'] ?? config('app.name') }}" />
        <meta property="og:type" content="@yield('og_type', 'website')" />
        <meta property="og:url" content="@yield('og_url', url()->current())" />
        <meta property="og:description" content="@yield('og_description', $data['web_setting']['description'] ?? config('app.name'))" />
        <meta property="og:image" content="@yield('og_image', asset('assets/app_logo/' . ($data['web_setting']['favicon'] ?? 'favicon.png')))" />

        <meta name="robots" content="@yield('meta_robots', 'index,follow')">

        <link type="image/png" href="{{ asset('assets/app_logo/' . ($data['web_setting']['favicon'] ?? 'favicon.png')) }}" rel="icon">

        <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />

        @stack('css')
        {{ $css ?? '' }}

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @if (filled($data['web_setting']['google_analytics'] ?? false))
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $data['web_setting']['google_analytics'] }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];

                function gtag() {
                    dataLayer.push(arguments);
                }
                gtag('js', new Date());
                gtag('config', '{{ $data['web_setting']['google_analytics'] }}');
            </script>
        @endif

        @if (filled($data['web_setting']['google_adsense'] ?? false))
            <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ $data['web_setting']['google_adsense'] }}" crossorigin="anonymous"></script>
        @endif

    </head>

    <body class="bg-back-base-200 dark:bg-dark-base-300 font-sans antialiased">
        <x-headerNav />
        <main class="w-full">
            {{ $slot }}
        </main>
        <x-footer />

        <!-- Supporting Components -->
        <x-toast />
        <x-alert-modal />
        <x-dependencies._messageAlert />

        <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
        @stack('javascript')
        {{ $javascript ?? '' }}
    </body>

</html>
