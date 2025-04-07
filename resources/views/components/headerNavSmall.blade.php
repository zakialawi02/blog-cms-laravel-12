<div class="w-full bg-gray-800 text-gray-200 dark:bg-gray-900">
    <div class="text-light container px-5 py-1 md:px-7">
        <div class="items-center text-sm md:flex md:justify-between">
            <div class="hidden md:flex md:gap-4">
                <a class="hover:text-primary" href="/">Home</a>
                <a class="hover:text-primary" href="https://zakialawi.my.id/">About</a>
                <a class="hover:text-primary" href="/p/contact">Contact</a>
            </div>
            <div class="flex flex-row items-center justify-between gap-4">
                <div class="flex gap-2 text-base">
                    <a class="hover:text-primary" href="{{ $data['web_setting']['link_fb'] }}" target="_blank" rel="noopener noreferrer"><i class="ri-facebook-circle-fill"></i></a>
                    <a class="hover:text-primary" href="{{ $data['web_setting']['link_ig'] }}" target="_blank" rel="noopener noreferrer"><i class="ri-instagram-fill"></i></a>
                    <a class="hover:text-primary" href="{{ $data['web_setting']['link_twitter'] }}" target="_blank" rel="noopener noreferrer"><i class="ri-twitter-x-fill"></i></a>
                    <button class="p-0.5 transition duration-300" id="theme-toggle" type="button" title="Toggle dark mode">
                        <svg class="hidden h-4 w-4 dark:text-white" id="icon-sun" data-name="Layer 1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.88 122.88">
                            <defs>
                                <style>
                                    .cls-1 {
                                        fill: #fcdb33;
                                    }
                                </style>
                            </defs>
                            <path class="cls-1" d="M30,13.21A3.93,3.93,0,1,1,36.8,9.27L41.86,18A3.94,3.94,0,1,1,35.05,22L30,13.21Zm31.45,13A35.23,35.23,0,1,1,36.52,36.52,35.13,35.13,0,0,1,61.44,26.2ZM58.31,4A3.95,3.95,0,1,1,66.2,4V14.06a3.95,3.95,0,1,1-7.89,0V4ZM87.49,10.1A3.93,3.93,0,1,1,94.3,14l-5.06,8.76a3.93,3.93,0,1,1-6.81-3.92l5.06-8.75ZM109.67,30a3.93,3.93,0,1,1,3.94,6.81l-8.75,5.06a3.94,3.94,0,1,1-4-6.81L109.67,30Zm9.26,28.32a3.95,3.95,0,1,1,0,7.89H108.82a3.95,3.95,0,1,1,0-7.89Zm-6.15,29.18a3.93,3.93,0,1,1-3.91,6.81l-8.76-5.06A3.93,3.93,0,1,1,104,82.43l8.75,5.06ZM92.89,109.67a3.93,3.93,0,1,1-6.81,3.94L81,104.86a3.94,3.94,0,0,1,6.81-4l5.06,8.76Zm-28.32,9.26a3.95,3.95,0,1,1-7.89,0V108.82a3.95,3.95,0,1,1,7.89,0v10.11Zm-29.18-6.15a3.93,3.93,0,0,1-6.81-3.91l5.06-8.76A3.93,3.93,0,1,1,40.45,104l-5.06,8.75ZM13.21,92.89a3.93,3.93,0,1,1-3.94-6.81L18,81A3.94,3.94,0,1,1,22,87.83l-8.76,5.06ZM4,64.57a3.95,3.95,0,1,1,0-7.89H14.06a3.95,3.95,0,1,1,0,7.89ZM10.1,35.39A3.93,3.93,0,1,1,14,28.58l8.76,5.06a3.93,3.93,0,1,1-3.92,6.81L10.1,35.39Z" />
                        </svg>
                        <svg class="h-4 w-4 dark:text-white" id="icon-moon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11.3807 2.01886C9.91573 3.38768 9 5.3369 9 7.49999C9 11.6421 12.3579 15 16.5 15C18.6631 15 20.6123 14.0843 21.9811 12.6193C21.6613 17.8537 17.3149 22 12 22C6.47715 22 2 17.5228 2 12C2 6.68514 6.14629 2.33869 11.3807 2.01886Z"></path>
                        </svg>
                    </button>
                </div>
                <div class="flex gap-2">
                    @auth
                        <a class="hover:text-accent" href="{{ route('admin.dashboard') }}">Dashboard</a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="hover:text-error" type="submit">Logout</button>
                        </form>
                    @else
                        <a class="hover:text-accent" href="{{ route('login') }}">Login</a>
                        <a class="hover:text-accent" href="{{ route('register') }}">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
