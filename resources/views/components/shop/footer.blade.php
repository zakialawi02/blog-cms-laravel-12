<!-- Shop Footer -->
<footer class="mt-16 bg-gray-900 text-gray-300">
    <!-- Main Footer Content -->
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
            <!-- Shop Info -->
            <div class="space-y-4">
                <div class="flex items-center space-x-3">
                    <x-application-logo class="h-auto max-h-8 max-w-10" />
                    <h3 class="text-xl font-bold text-white">{{ $data['web_setting']['web_name'] ?? config('app.name') }} Shop</h3>
                </div>
                <p class="text-sm text-gray-400">
                    Your trusted destination for quality digital products and services.
                    Discover amazing deals and premium products from verified sellers.
                </p>
                <div class="flex space-x-4">
                    <a class="text-gray-400 transition-colors duration-200 hover:text-white" href="#">
                        <i class="ri-facebook-fill text-xl"></i>
                    </a>
                    <a class="text-gray-400 transition-colors duration-200 hover:text-white" href="#">
                        <i class="ri-twitter-fill text-xl"></i>
                    </a>
                    <a class="text-gray-400 transition-colors duration-200 hover:text-white" href="#">
                        <i class="ri-instagram-fill text-xl"></i>
                    </a>
                    <a class="text-gray-400 transition-colors duration-200 hover:text-white" href="#">
                        <i class="ri-youtube-fill text-xl"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-white">Quick Links</h4>
                <ul class="space-y-2">
                    <li><a class="text-sm text-gray-400 transition-colors duration-200 hover:text-white" href="{{ route('shop.index') }}">All Products</a></li>
                    <li><a class="text-sm text-gray-400 transition-colors duration-200 hover:text-white" href="#">New Arrivals</a></li>
                    <li><a class="text-sm text-gray-400 transition-colors duration-200 hover:text-white" href="#">Best Sellers</a></li>
                    <li><a class="text-sm text-gray-400 transition-colors duration-200 hover:text-white" href="#">Sale Items</a></li>
                    <li><a class="text-sm text-gray-400 transition-colors duration-200 hover:text-white" href="#">Gift Cards</a></li>
                    <li><a class="text-sm text-gray-400 transition-colors duration-200 hover:text-white" href="{{ route('home') }}">Back to Blog</a></li>
                </ul>
            </div>

            <!-- Customer Service -->
            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-white">Customer Service</h4>
                <ul class="space-y-2">
                    <li><a class="text-sm text-gray-400 transition-colors duration-200 hover:text-white" href="#">Contact Us</a></li>
                    <li><a class="text-sm text-gray-400 transition-colors duration-200 hover:text-white" href="#">FAQ</a></li>
                    <li><a class="text-sm text-gray-400 transition-colors duration-200 hover:text-white" href="#">Shipping Info</a></li>
                    <li><a class="text-sm text-gray-400 transition-colors duration-200 hover:text-white" href="#">Return Policy</a></li>
                    <li><a class="text-sm text-gray-400 transition-colors duration-200 hover:text-white" href="#">Track Order</a></li>
                    <li><a class="text-sm text-gray-400 transition-colors duration-200 hover:text-white" href="#">Size Guide</a></li>
                </ul>
            </div>

            <!-- Newsletter & Payment -->
            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-white">Stay Updated</h4>
                <p class="text-sm text-gray-400">Subscribe to get special offers and updates.</p>
                <form class="space-y-2">
                    <div class="flex">
                        <input class="focus:ring-primary flex-1 rounded-l-lg border border-gray-700 bg-gray-800 px-3 py-2 text-white placeholder-gray-400 focus:border-transparent focus:ring-2" type="email" placeholder="Enter your email">
                        <button class="bg-primary hover:bg-accent rounded-r-lg px-4 py-2 text-white transition-colors duration-200" type="submit">
                            <i class="ri-mail-send-line"></i>
                        </button>
                    </div>
                </form>

                <!-- Payment Methods -->
                <div class="pt-4">
                    <h5 class="mb-2 text-sm font-semibold text-white">We Accept</h5>
                    <div class="flex space-x-2">
                        <div class="flex h-6 w-8 items-center justify-center rounded bg-blue-600">
                            <span class="text-xs font-bold text-white">V</span>
                        </div>
                        <div class="flex h-6 w-8 items-center justify-center rounded bg-red-600">
                            <span class="text-xs font-bold text-white">M</span>
                        </div>
                        <div class="flex h-6 w-8 items-center justify-center rounded bg-blue-500">
                            <span class="text-xs font-bold text-white">P</span>
                        </div>
                        <div class="flex h-6 w-8 items-center justify-center rounded bg-gray-700">
                            <span class="text-xs font-bold text-white">$</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trust Badges & Features -->
    <div class="border-t border-gray-800">
        <div class="container mx-auto px-4 py-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div class="flex items-center space-x-3">
                    <i class="ri-truck-line text-primary text-2xl"></i>
                    <div>
                        <h6 class="text-sm font-semibold text-white">Free Shipping</h6>
                        <p class="text-xs text-gray-400">On orders over $50</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <i class="ri-shield-check-line text-primary text-2xl"></i>
                    <div>
                        <h6 class="text-sm font-semibold text-white">Secure Payment</h6>
                        <p class="text-xs text-gray-400">100% secure transactions</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <i class="ri-refresh-line text-primary text-2xl"></i>
                    <div>
                        <h6 class="text-sm font-semibold text-white">Easy Returns</h6>
                        <p class="text-xs text-gray-400">30-day return policy</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <i class="ri-customer-service-2-line text-primary text-2xl"></i>
                    <div>
                        <h6 class="text-sm font-semibold text-white">24/7 Support</h6>
                        <p class="text-xs text-gray-400">Expert customer service</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Footer -->
    <div class="border-t border-gray-800">
        <div class="container mx-auto px-4 py-4">
            <div class="flex flex-col items-center justify-between md:flex-row">
                <div class="mb-4 text-sm text-gray-400 md:mb-0">
                    <p>&copy; {{ date('Y') }} {{ $data['web_setting']['web_name'] ?? config('app.name') }}. All rights reserved.</p>
                </div>
                <div class="flex space-x-6">
                    <a class="text-sm text-gray-400 transition-colors duration-200 hover:text-white" href="#">Privacy Policy</a>
                    <a class="text-sm text-gray-400 transition-colors duration-200 hover:text-white" href="#">Terms of Service</a>
                    <a class="text-sm text-gray-400 transition-colors duration-200 hover:text-white" href="#">Cookie Policy</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button class="bg-primary hover:bg-accent pointer-events-none fixed bottom-6 right-6 z-50 rounded-full p-3 text-white opacity-0 shadow-lg transition-all duration-200" id="back-to-top">
    <i class="ri-arrow-up-line text-xl"></i>
</button>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Back to top functionality
        const backToTopBtn = document.getElementById('back-to-top');

        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopBtn.classList.remove('opacity-0', 'pointer-events-none');
                backToTopBtn.classList.add('opacity-100');
            } else {
                backToTopBtn.classList.add('opacity-0', 'pointer-events-none');
                backToTopBtn.classList.remove('opacity-100');
            }
        });

        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
</script>
