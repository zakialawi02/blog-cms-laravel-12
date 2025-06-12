<div class="text-light sticky top-0 z-30 w-full bg-gray-800 dark:bg-gray-900">
    <div class="container px-5 py-1 md:px-7">
        <div class="items-center text-sm md:flex md:justify-between">
            <div class="hidden md:flex md:gap-4">
                @foreach ($data['menu']['header-top']['items'] ?? [] as $menu)
                    <a class="hover:text-primary dark:hover:text-primary" href={{ $menu['link'] }}>{{ $menu['label'] }}</a>
                @endforeach
            </div>
            <div class="flex flex-row items-center justify-between gap-4">
                <div class="flex gap-2 text-base">
                    <a class="hover:text-primary dark:hover:text-primary" href="{{ $data['web_setting']['link_fb'] ?? '#' }}" target="_blank" rel="noopener noreferrer"><i class="ri-facebook-circle-fill"></i><span class="sr-only">Follow me on Facebook</span></a>
                    <a class="hover:text-primary dark:hover:text-primary" href="{{ $data['web_setting']['link_ig'] ?? '#' }}" target="_blank" rel="noopener noreferrer"><i class="ri-instagram-fill"></i><span class="sr-only">Follow me on Instagram</span></a>
                    <a class="hover:text-primary dark:hover:text-primary" href="{{ $data['web_setting']['link_twitter'] ?? '#' }}" target="_blank" rel="noopener noreferrer"><i class="ri-twitter-x-fill"></i><span class="sr-only">Follow me on Twitter</span></a>

                    <!-- Dark/Light Mode Toggle -->
                    <button class="relative mx-1 h-6 w-10 rounded-full bg-gray-300 transition-colors duration-300 focus:outline-none dark:bg-gray-600" id="theme-toggle">
                        <span class="absolute left-1 top-1 h-4 w-4 transform rounded-full bg-white transition-transform duration-300 dark:translate-x-4 dark:bg-gray-200"></span>
                        <!-- Sun icon -->
                        <i class="ri-sun-fill absolute left-1 top-0.5 text-sm text-yellow-400 dark:hidden"><span class="sr-only">Light mode</span></i>
                        <!-- Moon icon -->
                        <i class="ri-moon-fill text-dark absolute right-1 top-0.5 hidden text-sm dark:block"><span class="sr-only">Dark mode</span></i>
                    </button>
                </div>
                <div class="flex gap-2">
                    @auth
                        <a class="hover:text-accent dark:hover:text-accent" href="{{ route('admin.dashboard') }}">Dashboard</a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="hover:text-error dark:hover:text-error" type="submit">Logout</button>
                        </form>
                    @else
                        <a class="hover:text-accent dark:hover:text-accent" href="{{ route('login') }}">Login</a>
                        <a class="hover:text-accent dark:hover:text-accent" href="{{ route('register') }}">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
