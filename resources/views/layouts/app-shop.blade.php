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
        <title>@yield('title') | {{ $data['web_setting']['web_name'] ?? config('app.name') }} - Shop</title>

        <meta name="description" content="@yield('meta_description', $data['web_setting']['description'] ?? '')">
        <meta name="author" content="@yield('meta_author', 'Zaki Alawi')">
        <meta name="keywords" content="@yield('meta_keywords', $data['web_setting']['keywords'] ?? 'Zaki Alawi, Blog, Shop, E-commerce')">
        <meta property="og:title" content="@yield('og_title', $data['web_setting']['web_name'] ?? config('app.name'))" />
        <meta property="og:type" content="@yield('og_type', 'website')" />
        <meta property="og:url" content="@yield('og_url', url()->current())" />
        <meta property="og:description" content="@yield('og_description', $data['web_setting']['description'] ?? config('app.name'))" />
        <meta property="og:image" content="@yield('og_image', asset('assets/app_logo/' . ($data['web_setting']['favicon'] ?? 'favicon.png')))" />

        <meta name="robots" content="@yield('meta_robots', 'index,follow')">
        <link href="{{ url()->current() }}" rel="canonical">

        <link type="image/png" href="{{ asset('assets/app_logo/' . ($data['web_setting']['favicon'] ?? 'favicon.png')) }}" rel="icon">

        <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />

        @stack('css')
        {{ $css ?? '' }}

        <!-- Shop-specific styles -->
        <style>
            /* E-commerce specific styles */
            .shop-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }

            .shop-nav {
                backdrop-filter: blur(10px);
                background: rgba(255, 255, 255, 0.95);
            }

            .dark .shop-nav {
                background: rgba(31, 41, 55, 0.95);
            }

            .product-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 1.5rem;
            }

            .filter-sidebar {
                min-height: calc(100vh - 200px);
                position: sticky;
                top: 120px;
            }

            .shop-container {
                min-height: calc(100vh - 80px);
            }

            .shop-breadcrumb {
                background: rgba(249, 250, 251, 0.8);
                backdrop-filter: blur(5px);
            }

            .dark .shop-breadcrumb {
                background: rgba(17, 24, 39, 0.8);
            }

            /* Shopping cart indicator */
            .cart-indicator {
                animation: pulse 2s infinite;
            }

            @keyframes pulse {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.7;
                }
            }

            /* Product card hover effects */
            .product-card {
                transition: all 0.3s ease;
            }

            .product-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            }

            /* Filter animations */
            .filter-section {
                transition: all 0.3s ease;
            }

            .filter-section.collapsed {
                max-height: 60px;
                overflow: hidden;
            }

            .filter-section.expanded {
                max-height: 500px;
            }
        </style>

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

        @if (filled($data['web_setting']['before_close_head'] ?? false))
            {!! $data['web_setting']['before_close_head'] ?? null !!}
        @endif

    </head>

    <body class="dark:bg-dark-base-300 bg-gray-50 font-sans antialiased">
        <!-- Shop-specific Header -->
        <x-shop.header />

        <!-- Shop Navigation -->
        <x-shop.navigation />

        <!-- Main Shop Content -->
        <main class="shop-container w-full">
            <!-- Breadcrumb Section -->
            @if (isset($breadcrumbs) || request()->routeIs('shop.*'))
                <section class="shop-breadcrumb border-b border-gray-200 py-3 dark:border-gray-700">
                    <div class="container mx-auto px-4">
                        @if (isset($breadcrumbs))
                            {{ $breadcrumbs }}
                        @else
                            <x-breadcrumb :items="[['text' => 'Home', 'link' => route('home')], ['text' => 'Shop', 'link' => route('shop.index')]]" />
                        @endif
                    </div>
                </section>
            @endif

            <!-- Main Content Area -->
            <div class="container mx-auto px-4 py-6">
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
                    <!-- Sidebar for filters (optional) -->
                    @if (isset($showSidebar) && $showSidebar)
                        <aside class="lg:col-span-1">
                            <div class="filter-sidebar">
                                <x-shop.sidebar />
                            </div>
                        </aside>
                        <div class="lg:col-span-3">
                            {{ $slot }}
                        </div>
                    @else
                        <div class="lg:col-span-4">
                            {{ $slot }}
                        </div>
                    @endif
                </div>
            </div>
        </main>

        <!-- Shop-specific Footer -->
        <x-shop.footer />

        <!-- Shopping Cart Sidebar (hidden by default) -->
        <x-shop.cart-sidebar />

        <!-- Supporting Components -->
        <x-toast />
        <x-alert-modal />
        <x-dependencies._messageAlert />

        <!-- Shop-specific JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

        <script>
            // Shop-specific JavaScript functionality
            document.addEventListener('DOMContentLoaded', function() {
                // Shopping cart functionality
                let cartCount = 0;

                // Add to cart function
                window.addToCart = function(productId, quantity = 1) {
                    cartCount += quantity;
                    updateCartIndicator();
                    showCartNotification();
                };

                // Update cart indicator
                function updateCartIndicator() {
                    const indicator = document.querySelector('.cart-count');
                    if (indicator) {
                        indicator.textContent = cartCount;
                        indicator.classList.add('cart-indicator');
                    }
                }

                // Show cart notification
                function showCartNotification() {
                    // You can integrate with your toast system here
                    console.log('Product added to cart!');
                }

                // Filter toggle functionality
                const filterToggles = document.querySelectorAll('.filter-toggle');
                filterToggles.forEach(toggle => {
                    toggle.addEventListener('click', function() {
                        const target = document.querySelector(this.dataset.target);
                        if (target) {
                            target.classList.toggle('collapsed');
                            target.classList.toggle('expanded');
                        }
                    });
                });

                // Search functionality
                const searchInput = document.querySelector('#shop-search');
                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        // Implement search functionality
                        console.log('Searching for:', this.value);
                    });
                }

                // Wishlist functionality
                window.toggleWishlist = function(productId) {
                    const wishlistBtn = document.querySelector(`[data-product-id="${productId}"]`);
                    if (wishlistBtn) {
                        wishlistBtn.classList.toggle('text-red-500');
                        wishlistBtn.classList.toggle('text-gray-400');
                    }
                };
            });
        </script>

        @stack('javascript')
        {{ $javascript ?? '' }}

        @if (filled($data['web_setting']['before_close_body'] ?? false))
            {!! $data['web_setting']['before_close_body'] ?? null !!}
        @endif
    </body>

</html>
