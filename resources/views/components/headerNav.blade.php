<header>
    <x-headerNavSmall />

    <!-- Search Overlay -->
    <div class="fixed left-0 top-0 z-[100] hidden h-screen w-full bg-black/90 backdrop-blur-md" id="search-overlay">
        <div class="absolute right-0 top-0 p-4">
            <button class="hover:text-primary text-2xl text-white" id="close-search-overlay" type="button">
                <i class="ri-close-circle-line"></i>
            </button>
        </div>
        <div class="absolute left-1/2 top-1/2 w-11/12 -translate-x-1/2 -translate-y-1/2 transform md:w-2/3 lg:w-1/2">
            <div class="rounded-lg p-4 shadow-xl">
                <form id="search-blog" action="/blog">
                    <div class="flex items-center overflow-hidden rounded-md bg-white px-1 shadow">
                        <input class="text-dark bg-light w-full border-0 px-3 py-3.5 text-base outline-none ring-0 focus:ring-0" id="search" name="search" type="search" value="" placeholder="Search">
                        <button class="text-light bg-secondary hover:bg-primary rounded px-3 py-2 font-semibold transition-all duration-500" type="submit">
                            <i class="ri-search-line"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-base-100 border-dark z-10 flex min-h-20 w-full items-center justify-between border-b border-opacity-50 px-6 md:bg-transparent md:px-14">
        <div class="text-dark max-w-[20rem] font-bold uppercase" id="logo-nav">
            <a class="inline-flex max-w-80 items-center text-xl" href="/blog">
                <x-application-logo class="h-auto max-w-14" />
                <span class="px-2" id="web_name">Blog</span>
            </a>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-xl font-medium md:hidden" id="hamburger">
                <button id="ham-btn"><i class="ri-menu-line"></i></button>
            </div>
            <div class="hover:text-accent search-btn text-xl font-medium md:hidden">
                <button><i class="ri-search-line"></i></button>
            </div>
        </div>
        <div class="hidden max-h-[100px] max-w-[800px] overflow-hidden md:block" id="ads-header">
            <!-- Google Ads -->
        </div>
    </div>

    <div class="md:border-dark flex-none items-center px-6 md:flex md:justify-between md:border-b md:border-opacity-50 md:px-14 md:py-1">
        <nav class="text-dark bg-base-100 container absolute left-0 right-0 z-10 hidden flex-col items-start p-3 text-[1.1rem] font-semibold uppercase md:relative md:top-0 md:flex md:w-[50rem] md:flex-row md:flex-wrap md:items-center md:bg-transparent md:p-0 md:opacity-100 lg:w-full" id="nav-menu">
            <a class="hover:text-accent p-2 duration-300" href="/">Home</a>
            <a class="hover:text-accent p-2 duration-300" href="/blog/categories/programming">Programming</a>
            <a class="hover:text-accent p-2 duration-300" href="/blog/categories/technology">Technology</a>
            <a class="hover:text-accent p-2 duration-300" href="/blog/categories/tutorial">Tutorial</a>
            <a class="hover:text-accent p-2 duration-300" href="/blog/categories/geography-geodesy">Earth</a>
        </nav>
        <div class="hover:text-accent search-btn hidden text-xl font-medium md:block">
            <button><i class="ri-search-line"></i></button>
        </div>
    </div>
</header>

@push('javascript')
    <script>
        $(".search-btn, #close-search-overlay").click(function(e) {
            e.preventDefault();
            $("#search-overlay").toggleClass('hidden');
        });
    </script>
@endpush
