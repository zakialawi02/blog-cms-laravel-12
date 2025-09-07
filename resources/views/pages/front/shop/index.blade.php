@section('title', $data['title'] ?? 'Shop')
@section('meta_description', 'Browse our collection of digital products and services')
@section('og_title', $data['title'] ?? 'Shop')

<x-app-shop-layout>
    <x-slot:showSidebar>{{ true }}</x-slot:showSidebar>

    <!-- Products Grid -->
    @if ($products->count() > 0)
        <div class="product-grid grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($products as $product)
                <x-shop.card-product class="product-card" :product="$product" />
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12 flex justify-center">
            {{ $products->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="py-16 text-center">
            <div class="mx-auto max-w-md">
                <i class="ri-shopping-bag-line mb-4 text-6xl text-gray-400"></i>
                <h3 class="mb-2 text-2xl font-semibold text-gray-700 dark:text-gray-300">
                    No Products Found
                </h3>
                <p class="mb-6 text-gray-500 dark:text-gray-400">
                    We're working on adding new products. Check back soon!
                </p>
                <a class="bg-primary hover:bg-accent inline-flex items-center rounded-lg px-6 py-3 text-white transition-colors duration-200" href="{{ route('home') }}">
                    <i class="ri-home-line mr-2"></i>
                    Back to Home
                </a>
            </div>
        </div>
    @endif

    <x-slot:javascript>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Listen for view changes from navigation
                document.addEventListener('viewChanged', function(e) {
                    const view = e.detail.view;
                    const productGrid = document.querySelector('.product-grid');

                    if (view === 'list') {
                        productGrid.classList.remove('sm:grid-cols-2', 'lg:grid-cols-3');
                        productGrid.classList.add('grid-cols-1');
                    } else {
                        productGrid.classList.add('sm:grid-cols-2', 'lg:grid-cols-3');
                    }
                });

                // Add to cart functionality for product cards
                document.querySelectorAll('.product-card').forEach(card => {
                    const cartBtn = card.querySelector('button');
                    if (cartBtn && cartBtn.textContent.includes('Cart')) {
                        cartBtn.addEventListener('click', function(e) {
                            e.preventDefault();

                            // Get product data from card
                            const productName = card.querySelector('h2 a').textContent.trim();
                            const productPrice = card.querySelector('[data-price]').getAttribute('data-price');
                            const productImage = card.querySelector('img').src;

                            // Add to cart using the shop layout's cart system
                            if (window.addToCartSidebar) {
                                window.addToCartSidebar({
                                    id: Date.now(), // Use timestamp as temp ID
                                    name: productName,
                                    price: parseFloat(productPrice),
                                    image: productImage,
                                    quantity: 1
                                });
                            }
                        });
                    }
                });
            });
        </script>
    </x-slot:javascript>
</x-app-shop-layout>
