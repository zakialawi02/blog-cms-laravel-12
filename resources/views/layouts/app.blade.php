<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <script>
            (function() {
                if (localStorage.getItem("theme") === "dark") {
                    document.documentElement.classList.add("dark");
                }
            })();
        </script>
        <title>@yield('title') • Dashboard | {{ $data['web_setting']['web_name'] ?? config('app.name') }}</title>

        <link type="image/png" href="{{ asset('assets/app_logo/' . ($data['web_setting']['favicon'] ?? 'favicon.png')) }}" rel="icon">

        <meta name="description" content="@yield('meta_description', 'Access your personal dashboard to manage your account, track progress, and explore features tailored for you.')">
        <meta name="author" content="@yield('meta_author', 'Zaki Alawi')">
        <meta name="keywords" content="@yield('meta_keywords', 'dashboard, user panel, account management, analytics, settings, profile')">
        <meta property="og:title" content="@yield('og_title', config('app.name'))" />
        <meta property="og:type" content="@yield('og_type', 'website')" />
        <meta property="og:url" content="@yield('og_url', url()->current())" />
        <meta property="og:description" content="@yield('og_description', config('app.name'))" />
        <meta property="og:image" content="@yield('og_image', asset('assets/app_logo/favicon.png'))" />

        <meta name="robots" content="@yield('meta_robots', 'noindex, nofollow')">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />

        @stack('css')
        {{ $css ?? '' }}

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
        @vite(['resources/css/app.css', 'resources/js/app-dashboard.js', 'resources/js/app.js'])
    </head>

    <body class="font-sans antialiased">
        <div class="sticky inset-x-0 top-0 z-20">
            <!-- ========== HEADER ========== -->
            <x-dashboard.app-header />
            <!-- ========== END HEADER ========== -->

            <!-- Breadcrumb Section -->
            <div class="relative -mt-px">
                <div class="dark:bg-back-dark-base-200 dark:border-back-dark-base-300 z-20 border-y border-gray-200 bg-white px-4 sm:px-6 lg:hidden lg:px-8">
                    <div class="flex items-center py-2">
                        <!-- Navigation Toggle -->
                        <button class="focus:outline-hidden dark:border-back-dark-light dark:text-back-dark-light dark:hover:text-back-dark-light dark:focus:text-back-dark-light flex size-8 items-center justify-center gap-x-2 rounded-lg border border-gray-200 text-gray-800 hover:text-gray-500 focus:text-gray-500" data-drawer-target="sidebar-multi-level-sidebar" data-drawer-toggle="sidebar-multi-level-sidebar" aria-controls="sidebar-multi-level-sidebar" aria-expanded="false">
                            <span class="sr-only">Toggle Navigation</span>
                            <i class="ri-sidebar-unfold-line text-xl"></i>
                        </button>
                        <!-- End Navigation Toggle -->

                        <!-- Breadcrumb -->
                        <x-breadcrumb class="ms-3 w-[calc(100vw-5rem)] flex-auto" :items="generate_breadcrumbs()" />

                        <!-- End Breadcrumb -->
                    </div>
                </div>
            </div>
        </div>
        <!-- End Breadcrumb Section -->

        <!-- Sidebar -->
        <x-dashboard.app-sidebar />
        <!-- End Sidebar -->

        <!-- ========== MAIN CONTENT ========== -->
        <!-- Content -->
        <main class="bg-back-base-200 dark:bg-back-dark-base-200/85 dark:text-back-light min-h-screen w-full text-gray-900 lg:ps-64">
            <div class="space-y-1 p-2 sm:p-0">
                <!-- your content goes here ... -->

                {{ $slot }}

            </div>
        </main>
        <!-- End Content -->
        <!-- ========== END MAIN CONTENT ========== -->

        <!-- Supporting Components -->
        <x-toast />
        <x-alert-modal />
        <x-dependencies._messageAlert />

        <script>
            $(document).on("click", ".zk-delete-data", function(e) {
                e.preventDefault();
                var form = $(this).closest('form'); // Get the closest form
                ZkPopAlert.show({
                    message: "Are you sure you want to delete this data?",
                    confirmText: "Yes, delete it",
                    cancelText: "No, cancel",
                    onConfirm: function() { // Use function() instead of arrow function for better scope handling
                        form.submit();
                    }
                });
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
        @stack('javascript')
        {{ $javascript ?? '' }}
        <script>
            $(document).ready(function() {
                let formChanged = false;
                const zkFormInputs = document.querySelectorAll('.my-form-input');
                zkFormInputs.forEach(input => {
                    input.addEventListener('change', () => {
                        formChanged = true;
                    });
                });
                window.addEventListener('beforeunload', function(e) {
                    if (!formChanged) return undefined;
                    // Cancel the event as per the standard.
                    e.preventDefault();
                    // Chrome requires returnValue to be set.
                    e.returnValue = '';
                    return 'Are you sure you want to leave? Changes you made may not be saved.';
                });
                const zkForm = document.querySelectorAll('.my-form-input')[0];
                if (zkForm) {
                    zkForm.addEventListener('submit', function() {
                        formChanged = false;
                    });
                }
            });
        </script>
    </body>

</html>
