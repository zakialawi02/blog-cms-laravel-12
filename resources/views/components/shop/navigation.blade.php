<!-- Shop Navigation -->
<nav class="shop-nav sticky top-0 z-40 border-b border-gray-200 dark:border-gray-700">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between py-3">
            <!-- Shop Categories -->
            <div class="flex items-center space-x-6">
                <a class="{{ request()->routeIs('shop.index') ? 'bg-primary text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} flex items-center space-x-1 rounded-lg px-3 py-2 transition-colors duration-200" href="{{ route('shop.index') }}">
                    <i class="ri-store-line"></i>
                    <span>All Products</span>
                </a>

                <!-- Categories can be dynamically loaded here -->
                <a class="flex items-center space-x-1 rounded-lg px-3 py-2 text-gray-700 transition-colors duration-200 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700" href="#">
                    <i class="ri-computer-line"></i>
                    <span>Electronics</span>
                </a>

                <a class="flex items-center space-x-1 rounded-lg px-3 py-2 text-gray-700 transition-colors duration-200 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700" href="#">
                    <i class="ri-book-line"></i>
                    <span>Digital</span>
                </a>

                <a class="flex items-center space-x-1 rounded-lg px-3 py-2 text-gray-700 transition-colors duration-200 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700" href="#">
                    <i class="ri-gamepad-line"></i>
                    <span>Gaming</span>
                </a>
            </div>

            <!-- Sort and Filter Options -->
            <div class="hidden items-center space-x-4 md:flex">
                <!-- Sort Dropdown -->
                <div class="relative">
                    <select class="focus:ring-primary appearance-none rounded-lg border border-gray-300 bg-white px-4 py-2 pr-8 text-sm focus:border-transparent focus:ring-2 dark:border-gray-600 dark:bg-gray-800">
                        <option value="latest">Latest</option>
                        <option value="price_low">Price: Low to High</option>
                        <option value="price_high">Price: High to Low</option>
                        <option value="popular">Most Popular</option>
                        <option value="rating">Best Rating</option>
                    </select>
                    <i class="ri-arrow-down-s-line pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 transform text-gray-400"></i>
                </div>

                <!-- View Toggle -->
                <div class="flex items-center rounded-lg bg-gray-100 p-1 dark:bg-gray-700">
                    <button class="view-toggle active rounded p-2 text-gray-600 transition-colors duration-200 hover:bg-white dark:text-gray-300 dark:hover:bg-gray-600" data-view="grid">
                        <i class="ri-grid-line"></i>
                    </button>
                    <button class="view-toggle rounded p-2 text-gray-600 transition-colors duration-200 hover:bg-white dark:text-gray-300 dark:hover:bg-gray-600" data-view="list">
                        <i class="ri-list-check-2"></i>
                    </button>
                </div>

                <!-- Filter Toggle -->
                <button class="flex items-center space-x-1 rounded-lg border border-gray-300 px-3 py-2 text-gray-700 transition-colors duration-200 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700" id="filter-toggle">
                    <i class="ri-filter-line"></i>
                    <span>Filters</span>
                </button>
            </div>

            <!-- Mobile Sort and Filter -->
            <div class="flex items-center space-x-2 md:hidden">
                <button class="rounded-lg border border-gray-300 p-2 text-gray-700 transition-colors duration-200 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700" id="mobile-sort-toggle">
                    <i class="ri-sort-asc"></i>
                </button>
                <button class="rounded-lg border border-gray-300 p-2 text-gray-700 transition-colors duration-200 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700" id="mobile-filter-toggle">
                    <i class="ri-filter-line"></i>
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- Quick Filter Tags -->
<div class="border-b border-gray-200 bg-gray-50 py-2 dark:border-gray-700 dark:bg-gray-800">
    <div class="container mx-auto px-4">
        <div class="flex items-center space-x-3 overflow-x-auto py-1">
            <span class="whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">Quick filters:</span>
            <div class="flex space-x-2">
                <button class="whitespace-nowrap rounded-full bg-blue-100 px-3 py-1 text-xs text-blue-800 transition-colors duration-200 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800">
                    On Sale
                </button>
                <button class="whitespace-nowrap rounded-full bg-green-100 px-3 py-1 text-xs text-green-800 transition-colors duration-200 hover:bg-green-200 dark:bg-green-900 dark:text-green-200 dark:hover:bg-green-800">
                    In Stock
                </button>
                <button class="whitespace-nowrap rounded-full bg-purple-100 px-3 py-1 text-xs text-purple-800 transition-colors duration-200 hover:bg-purple-200 dark:bg-purple-900 dark:text-purple-200 dark:hover:bg-purple-800">
                    New Arrivals
                </button>
                <button class="whitespace-nowrap rounded-full bg-orange-100 px-3 py-1 text-xs text-orange-800 transition-colors duration-200 hover:bg-orange-200 dark:bg-orange-900 dark:text-orange-200 dark:hover:bg-orange-800">
                    Best Sellers
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // View toggle functionality
        const viewToggles = document.querySelectorAll('.view-toggle');
        viewToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                viewToggles.forEach(t => t.classList.remove('active', 'bg-white', 'dark:bg-gray-600'));
                this.classList.add('active', 'bg-white', 'dark:bg-gray-600');

                const view = this.dataset.view;
                // Emit custom event for view change
                document.dispatchEvent(new CustomEvent('viewChanged', {
                    detail: {
                        view
                    }
                }));
            });
        });

        // Quick filter functionality
        const quickFilters = document.querySelectorAll('.bg-blue-100, .bg-green-100, .bg-purple-100, .bg-orange-100');
        quickFilters.forEach(filter => {
            filter.addEventListener('click', function() {
                this.classList.toggle('ring-2');
                this.classList.toggle('ring-offset-2');
                this.classList.toggle('ring-blue-500');
            });
        });
    });
</script>
