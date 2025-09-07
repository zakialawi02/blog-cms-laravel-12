<!-- Shop Header -->
<header class="shop-header text-white">
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <!-- Logo and Branding -->
            <div class="flex items-center space-x-4">
                <a class="flex items-center space-x-3" href="{{ route('home') }}">
                    @if ($data['web_setting']['web_name_variant'] == '1')
                        <x-application-logo class="h-auto max-h-10 max-w-12" />
                        <span class="text-xl font-bold">{{ $data['web_setting']['web_name'] ?? config('app.name') }}</span>
                    @elseif ($data['web_setting']['web_name_variant'] == '2')
                        <x-application-logo class="h-auto max-h-10 max-w-12" />
                    @else
                        <x-application-logo class="h-auto max-h-10 max-w-12" />
                        <span class="text-xl font-bold">{{ $data['web_setting']['web_name'] ?? config('app.name') }}</span>
                    @endif
                    <span class="rounded-full bg-white/20 px-2 py-1 text-sm">Shop</span>
                </a>
            </div>

            <!-- Shop Search Bar -->
            <div class="mx-8 hidden max-w-lg flex-1 md:flex">
                <div class="relative w-full">
                    <form class="flex" action="{{ route('shop.index') }}" method="GET">
                        <input class="flex-1 rounded-l-lg border-0 bg-white/90 px-4 py-2 text-gray-900 placeholder-gray-500 focus:bg-white focus:ring-2 focus:ring-white/50" id="shop-search" name="search" type="text" value="{{ request('search') }}" placeholder="Search products...">
                        <button class="rounded-r-lg bg-white/20 px-6 py-2 transition-colors duration-200 hover:bg-white/30" type="submit">
                            <i class="ri-search-line text-white"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Shop Actions -->
            <div class="flex items-center space-x-4">
                <!-- User Account -->
                @auth
                    <div class="hidden items-center space-x-2 md:flex">
                        <a class="flex items-center space-x-2 transition-colors duration-200 hover:text-gray-200" href="{{ route('admin.dashboard') }}">
                            <i class="ri-user-line"></i>
                            <span class="text-sm">{{ Auth::user()->name }}</span>
                        </a>
                    </div>
                @else
                    <div class="hidden items-center space-x-4 md:flex">
                        <a class="text-sm transition-colors duration-200 hover:text-gray-200" href="{{ route('login') }}">Login</a>
                        <a class="rounded-lg bg-white/20 px-3 py-1 text-sm transition-colors duration-200 hover:bg-white/30" href="{{ route('register') }}">Register</a>
                    </div>
                @endauth

                <!-- Wishlist -->
                <button class="relative rounded-lg p-2 transition-colors duration-200 hover:bg-white/10">
                    <i class="ri-heart-line text-xl"></i>
                    <span class="wishlist-count absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs">0</span>
                </button>

                <!-- Shopping Cart -->
                <button class="relative rounded-lg p-2 transition-colors duration-200 hover:bg-white/10" id="cart-toggle">
                    <i class="ri-shopping-cart-line text-xl"></i>
                    <span class="cart-count absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs">0</span>
                </button>

                <!-- Mobile Menu Toggle -->
                <button class="rounded-lg p-2 transition-colors duration-200 hover:bg-white/10 md:hidden" id="mobile-menu-toggle">
                    <i class="ri-menu-line text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Search Bar -->
        <div class="mt-4 md:hidden">
            <form class="flex" action="{{ route('shop.index') }}" method="GET">
                <input class="flex-1 rounded-l-lg border-0 bg-white/90 px-4 py-2 text-gray-900 placeholder-gray-500 focus:bg-white focus:ring-2 focus:ring-white/50" name="search" type="text" value="{{ request('search') }}" placeholder="Search products...">
                <button class="rounded-r-lg bg-white/20 px-6 py-2 transition-colors duration-200 hover:bg-white/30" type="submit">
                    <i class="ri-search-line text-white"></i>
                </button>
            </form>
        </div>

        <!-- Mobile Menu -->
        <div class="mt-4 hidden md:hidden" id="mobile-menu">
            <div class="flex flex-col space-y-2 border-t border-white/20 pt-4">
                @auth
                    <a class="flex items-center space-x-2 py-2 transition-colors duration-200 hover:text-gray-200" href="{{ route('admin.dashboard') }}">
                        <i class="ri-user-line"></i>
                        <span>{{ Auth::user()->name }}</span>
                    </a>
                @else
                    <a class="py-2 transition-colors duration-200 hover:text-gray-200" href="{{ route('login') }}">Login</a>
                    <a class="inline-block rounded-lg bg-white/20 px-3 py-2 text-center transition-colors duration-200 hover:bg-white/30" href="{{ route('register') }}">Register</a>
                @endauth
            </div>
        </div>
    </div>
</header>

<script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');

        if (mobileMenuToggle && mobileMenu) {
            mobileMenuToggle.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }
    });
</script>
