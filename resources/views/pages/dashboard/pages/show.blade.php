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
        <title>@yield('title') {{ $page->title }} | {{ $data['web_setting']['web_name'] ?? config('app.name') }}</title>

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

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>

        <!-- grapesjs -->
        <link href="https://unpkg.com/grapesjs/dist/css/grapes.min.css" rel="stylesheet">
        <script src="https://unpkg.com/grapesjs"></script>
        <script src="https://unpkg.com/grapesjs-blocks-basic"></script>
        <script src="https://unpkg.com/grapesjs-blocks-flexbox"></script>
        <script src="https://unpkg.com/grapesjs-navbar"></script>
        <script src="https://unpkg.com/grapesjs-style-gradient"></script>
        <script src="https://unpkg.com/grapesjs-component-countdown"></script>
        <script src="https://unpkg.com/grapesjs-plugin-forms"></script>
        <script src="https://unpkg.com/grapesjs-style-filter"></script>
        <script src="https://unpkg.com/grapesjs-tabs"></script>
        <script src="https://unpkg.com/grapesjs-tooltip"></script>
        <script src="https://unpkg.com/grapesjs-custom-code"></script>
        <script src="https://unpkg.com/grapesjs-touch"></script>
        <script src="https://unpkg.com/grapesjs-parser-postcss"></script>
        <script src="https://unpkg.com/grapesjs-typed"></script>
        <script src="https://unpkg.com/grapesjs-style-bg"></script>
        <script src="https://unpkg.com/grapesjs-tui-image-editor"></script>
        <script src="https://unpkg.com/grapesjs-ui-suggest-classes"></script>
        <script src="https://unpkg.com/grapesjs-ga"></script>
        <script src="https://unpkg.com/grapesjs-component-twitch"></script>
        <script src="https://unpkg.com/grapesjs-user-blocks"></script>
        <script src="https://unpkg.com/grapesjs-chartjs-plugin"></script>
        <script src="https://unpkg.com/grapesjs-tailwindcss-plugin"></script>
        {{-- <script src="https://unpkg.com/grapesjs-tailwind"></script> --}}
        <script src="https://unpkg.com/grapesjs-preset-webpage@1.0.2"></script>

        <style>
            .lc {
                display: flex;
                justify-content: center;
                align-items: center;
                /* position: fixed; */
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 1;
            }

            .spn {
                width: 50px;
                padding: 8px;
                aspect-ratio: 1;
                border-radius: 50%;
                background: #196cca;
                --_m:
                    conic-gradient(#0000 10%, #000),
                    linear-gradient(#000 0 0) content-box;
                -webkit-mask: var(--_m);
                mask: var(--_m);
                -webkit-mask-composite: source-out;
                mask-composite: subtract;
                animation: s3 1s infinite linear;
            }

            @keyframes s3 {
                to {
                    transform: rotate(1turn)
                }
            }
        </style>

        @stack('css')

        @if ($page->isFullWidth == 1)
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>



    <body class="bg-back-base-200 dark:bg-dark-base-300 font-sans antialiased">
        @if ($page->isFullWidth == 1)
            <!-- NAVBAR -->
            <x-headerNav />
        @endif

        <main class="@if ($page->isFullWidth == 1) w-full p-6 md:p-10 @endif">

            <!-- Loader -->
            <div class="lc" id="lspn">
                <div class="spn"></div>
            </div>

            {{-- @dd($page) --}}

            <div id="gjs"></div>
            <div id="gjscss"></div>

            @if ($page->isFullWidth == 1)
                <!-- Footer -->
                <x-footer />
            @endif
        </main>

        <!-- Supporting Components -->
        <x-toast />
        <x-alert-modal />
        <x-dependencies._messageAlert />

        <script>
            const escapeName = (name) => `${name}`.trim().replace(/([^a-z0-9\w-:/]+)/gi, '-');
            const projectId = '{{ $page->id }}';
            const loadProjectEndpoint = `{{ url('/dashboard/pages/${projectId}/load-project') }}`;
            const storeProjectEndpoint = `{{ url('/dashboard/pages/${projectId}/store-project') }}`;

            $.ajax({
                type: "get",
                url: loadProjectEndpoint,
                dataType: "json",
                success: function(response) {
                    const projectData = response.data;
                    // console.log(projectData);
                    $('#lspn').remove();
                    const editor = grapesjs.init({
                        headless: true,
                        plugins: [
                            'gjs-blocks-basic',
                            'grapesjs-plugin-forms',
                            'grapesjs-blocks-flexbox',
                            'grapesjs-component-countdown',
                            'grapesjs-tabs',
                            'grapesjs-custom-code',
                            'grapesjs-touch',
                            'grapesjs-navbar',
                            'grapesjs-style-gradient',
                            'grapesjs-parser-postcss',
                            'grapesjs-tooltip',
                            'grapesjs-tui-image-editor',
                            'grapesjs-typed',
                            'grapesjs-style-bg',
                            'grapesjs-ui-suggest-classes',
                            'grapesjs-style-filter',
                            'grapesjs-user-blocks',
                            'grapesjs-ga',
                            'grapesjs-component-twitch',
                            'grapesjs-chartjs-plugin',
                            'grapesjs-tailwindcss-plugin',
                            'grapesjs-rellax',
                            // 'grapesjs-tailwind',
                            'grapesjs-preset-webpage',
                        ],
                    })
                    editor.loadProjectData(projectData);
                    const html = editor.getHtml();
                    const css = editor.getCss();

                    // console.log('html:', html);
                    // console.log('css:', css);
                    $("#gjs").append(html);
                    const style = document.createElement('style');
                    style.type = 'text/css';
                    style.innerHTML = css;
                    document.getElementsByTagName('head')[0].appendChild(style);
                    // $("#gjscss").append(css);
                },
                error: function(error) {
                    console.error(error);
                }
            });
        </script>

        @stack('javascript')
    </body>

</html>
