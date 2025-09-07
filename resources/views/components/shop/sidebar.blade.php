<!-- Shop Sidebar with Filters -->
<div class="space-y-6">
    <!-- Price Range Filter -->
    <div class="filter-section rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
        <div class="mb-3 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Price Range</h3>
            <button class="filter-toggle text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" data-target=".price-filter">
                <i class="ri-arrow-down-s-line"></i>
            </button>
        </div>
        <div class="price-filter space-y-3">
            <div class="space-x-3">
                <input class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" type="number" placeholder="Min (USD 10)" min="0">
                <span class="flex items-center justify-center text-gray-500">To</span>
                <input class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" type="number" placeholder="Max (USD 1000)" min="0">
            </div>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input class="text-primary focus:ring-primary rounded border-gray-300" type="checkbox">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Under $50</span>
                </label>
                <label class="flex items-center">
                    <input class="text-primary focus:ring-primary rounded border-gray-300" type="checkbox">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">$50 - $100</span>
                </label>
                <label class="flex items-center">
                    <input class="text-primary focus:ring-primary rounded border-gray-300" type="checkbox">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">$100 - $500</span>
                </label>
                <label class="flex items-center">
                    <input class="text-primary focus:ring-primary rounded border-gray-300" type="checkbox">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Over $500</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Categories Filter -->
    <div class="filter-section rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
        <div class="mb-3 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Categories</h3>
            <button class="filter-toggle text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" data-target=".category-filter">
                <i class="ri-arrow-down-s-line"></i>
            </button>
        </div>
        <div class="category-filter space-y-2">
            <label class="flex items-center justify-between">
                <div class="flex items-center">
                    <input class="text-primary focus:ring-primary rounded border-gray-300" type="checkbox">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Electronics</span>
                </div>
                <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-500 dark:bg-gray-700">24</span>
            </label>
            <label class="flex items-center justify-between">
                <div class="flex items-center">
                    <input class="text-primary focus:ring-primary rounded border-gray-300" type="checkbox">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Digital Products</span>
                </div>
                <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-500 dark:bg-gray-700">18</span>
            </label>
            <label class="flex items-center justify-between">
                <div class="flex items-center">
                    <input class="text-primary focus:ring-primary rounded border-gray-300" type="checkbox">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Gaming</span>
                </div>
                <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-500 dark:bg-gray-700">12</span>
            </label>
            <label class="flex items-center justify-between">
                <div class="flex items-center">
                    <input class="text-primary focus:ring-primary rounded border-gray-300" type="checkbox">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Software</span>
                </div>
                <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-500 dark:bg-gray-700">8</span>
            </label>
            <label class="flex items-center justify-between">
                <div class="flex items-center">
                    <input class="text-primary focus:ring-primary rounded border-gray-300" type="checkbox">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Accessories</span>
                </div>
                <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-500 dark:bg-gray-700">15</span>
            </label>
        </div>
    </div>

    <!-- Brand Filter -->
    <div class="filter-section rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
        <div class="mb-3 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Sellers</h3>
            <button class="filter-toggle text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" data-target=".brand-filter">
                <i class="ri-arrow-down-s-line"></i>
            </button>
        </div>
        <div class="brand-filter space-y-2">
            <label class="flex items-center">
                <input class="text-primary focus:ring-primary rounded border-gray-300" type="checkbox">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Tech Store</span>
            </label>
            <label class="flex items-center">
                <input class="text-primary focus:ring-primary rounded border-gray-300" type="checkbox">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Digital Hub</span>
            </label>
            <label class="flex items-center">
                <input class="text-primary focus:ring-primary rounded border-gray-300" type="checkbox">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Gaming World</span>
            </label>
        </div>
    </div>

    <!-- Rating Filter -->
    <div class="filter-section rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
        <div class="mb-3 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Rating</h3>
            <button class="filter-toggle text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" data-target=".rating-filter">
                <i class="ri-arrow-down-s-line"></i>
            </button>
        </div>
        <div class="rating-filter space-y-2">
            <label class="flex items-center">
                <input class="text-primary focus:ring-primary border-gray-300" name="rating" type="radio">
                <div class="ml-2 flex items-center">
                    <div class="flex text-yellow-400">
                        <i class="ri-star-fill"></i>
                        <i class="ri-star-fill"></i>
                        <i class="ri-star-fill"></i>
                        <i class="ri-star-fill"></i>
                        <i class="ri-star-fill"></i>
                    </div>
                    <span class="ml-1 text-sm text-gray-700 dark:text-gray-300">5 Stars</span>
                </div>
            </label>
            <label class="flex items-center">
                <input class="text-primary focus:ring-primary border-gray-300" name="rating" type="radio">
                <div class="ml-2 flex items-center">
                    <div class="flex text-yellow-400">
                        <i class="ri-star-fill"></i>
                        <i class="ri-star-fill"></i>
                        <i class="ri-star-fill"></i>
                        <i class="ri-star-fill"></i>
                        <i class="ri-star-line"></i>
                    </div>
                    <span class="ml-1 text-sm text-gray-700 dark:text-gray-300">4+ Stars</span>
                </div>
            </label>
            <label class="flex items-center">
                <input class="text-primary focus:ring-primary border-gray-300" name="rating" type="radio">
                <div class="ml-2 flex items-center">
                    <div class="flex text-yellow-400">
                        <i class="ri-star-fill"></i>
                        <i class="ri-star-fill"></i>
                        <i class="ri-star-fill"></i>
                        <i class="ri-star-line"></i>
                        <i class="ri-star-line"></i>
                    </div>
                    <span class="ml-1 text-sm text-gray-700 dark:text-gray-300">3+ Stars</span>
                </div>
            </label>
        </div>
    </div>

    <!-- Availability Filter -->
    <div class="filter-section rounded-lg bg-white p-4 shadow-sm dark:bg-gray-800">
        <div class="mb-3 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Availability</h3>
            <button class="filter-toggle text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" data-target=".availability-filter">
                <i class="ri-arrow-down-s-line"></i>
            </button>
        </div>
        <div class="availability-filter space-y-2">
            <label class="flex items-center">
                <input class="text-primary focus:ring-primary rounded border-gray-300" type="checkbox">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">In Stock</span>
            </label>
            <label class="flex items-center">
                <input class="text-primary focus:ring-primary rounded border-gray-300" type="checkbox">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">On Sale</span>
            </label>
            <label class="flex items-center">
                <input class="text-primary focus:ring-primary rounded border-gray-300" type="checkbox">
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Free Shipping</span>
            </label>
        </div>
    </div>

    <!-- Clear Filters Button -->
    <div class="space-y-3">
        <button class="bg-primary hover:bg-accent w-full rounded-lg px-4 py-2 text-white transition-colors duration-200">
            Apply Filters
        </button>
        <button class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition-colors duration-200 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
            Clear All
        </button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter toggle functionality
        const filterToggles = document.querySelectorAll('.filter-toggle');
        filterToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const target = document.querySelector(this.dataset.target);
                const icon = this.querySelector('i');

                if (target) {
                    target.classList.toggle('hidden');
                    icon.classList.toggle('ri-arrow-down-s-line');
                    icon.classList.toggle('ri-arrow-up-s-line');
                }
            });
        });

        // Filter change handlers
        const filterInputs = document.querySelectorAll('.filter-section input');
        filterInputs.forEach(input => {
            input.addEventListener('change', function() {
                // Emit filter change event
                document.dispatchEvent(new CustomEvent('filterChanged', {
                    detail: {
                        type: this.type,
                        name: this.name,
                        value: this.value,
                        checked: this.checked
                    }
                }));
            });
        });
    });
</script>
