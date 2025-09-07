@section('title', $product->product_name)
@section('meta_description', $product->description)
@section('og_title', $product->product_name)
@section('og_description', $product->description)
@section('og_image', $product->thumbnail ? asset('storage/products/' . $product->thumbnail) : '')

<x-app-shop-layout>
    <x-slot:css>
        <!-- Swiper CSS for image gallery -->
        <link href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" rel="stylesheet" />

        <style>
            /* Quantity control styling */
            .quantity-input::-webkit-outer-spin-button,
            .quantity-input::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

            .quantity-input[type=number] {
                -moz-appearance: textfield;
            }

            /* Button hover effects */
            .quantity-decrease:hover,
            .quantity-increase:hover {
                transform: translateY(-1px);
            }

            .quantity-decrease:active,
            .quantity-increase:active {
                transform: translateY(0);
            }

            /* Disabled state */
            .quantity-decrease:disabled,
            .quantity-increase:disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }
        </style>
    </x-slot:css>

    <!-- Breadcrumb -->
    <nav class="container mx-auto px-4 py-4">
        @php
            $breadcrumbItems = [['text' => 'Home', 'link' => route('home')], ['text' => 'Shop', 'link' => route('shop.index')], ['text' => $product->product_name, 'link' => '#']];
        @endphp
        <x-breadcrumb :items="$breadcrumbItems" />
    </nav>

    <!-- Product Detail Section -->
    <section class="container mx-auto px-4 pb-12">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            <!-- Product Images -->
            <div class="product-images">
                <!-- Main Image Gallery -->
                <div class="swiper product-main-swiper mb-4">
                    <div class="swiper-wrapper">
                        @if ($product->productImages && $product->productImages->count() > 0)
                            @foreach ($product->productImages as $image)
                                <div class="swiper-slide">
                                    <img class="h-96 w-full rounded-lg object-cover object-center md:h-[500px]" src="{{ asset('storage/products/' . $image->image_path) }}" alt="{{ $product->product_name }}" onerror="this.onerror=null;this.src='{{ asset('assets/img/image-placeholder.png') }}'">
                                </div>
                            @endforeach
                        @elseif($product->thumbnail)
                            <div class="swiper-slide">
                                <img class="h-96 w-full rounded-lg object-cover object-center md:h-[500px]" src="{{ asset('storage/products/' . $product->thumbnail) }}" alt="{{ $product->product_name }}" onerror="this.onerror=null;this.src='{{ asset('assets/img/image-placeholder.png') }}'">
                            </div>
                        @else
                            <div class="swiper-slide">
                                <div class="flex h-96 w-full items-center justify-center rounded-lg bg-gray-200 md:h-[500px] dark:bg-gray-700">
                                    <i class="ri-image-line text-6xl text-gray-400"></i>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Navigation buttons -->
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>

                    <!-- Pagination -->
                    <div class="swiper-pagination"></div>
                </div>

                <!-- Thumbnail Gallery -->
                @if ($product->productImages && $product->productImages->count() > 1)
                    <div class="swiper product-thumbs-swiper">
                        <div class="swiper-wrapper">
                            @foreach ($product->productImages as $image)
                                <div class="swiper-slide">
                                    <img class="h-20 w-20 cursor-pointer rounded-lg object-cover object-center opacity-60 hover:opacity-100" src="{{ asset('storage/products/' . $image->image_path) }}" alt="{{ $product->product_name }}" onerror="this.onerror=null;this.src='{{ asset('assets/img/image-placeholder.png') }}'">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Product Information -->
            <div class="product-info">
                <!-- Product Title and Rating -->
                <div class="mb-6">
                    <h1 class="mb-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
                        {{ $product->product_name }}
                    </h1>

                    <!-- Seller Info -->
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                        <i class="ri-user-line mr-1"></i>
                        <span>Sold by <strong>{{ $product->user->name }}</strong></span>
                        <span class="mx-2">•</span>
                        <span>{{ $product->sales_count ?? 0 }} sales</span>
                    </div>
                </div>

                <!-- Price Section -->
                <div class="mb-6">
                    <div class="flex items-center space-x-4">
                        @if ($product->discount_price && $product->discount_price < $product->price)
                            <span class="text-3xl font-bold text-red-600 dark:text-red-400">
                                {{ $product->currency }} {{ number_format($product->discount_price, 2) }}
                            </span>
                            <span class="text-xl text-gray-500 line-through">
                                {{ $product->currency }} {{ number_format($product->price, 2) }}
                            </span>
                            <span class="rounded-full bg-red-500 px-3 py-1 text-sm font-semibold text-white">
                                @php
                                    $discountPercent = round((($product->price - $product->discount_price) / $product->price) * 100);
                                @endphp
                                {{ $discountPercent }}% OFF
                            </span>
                        @else
                            <span class="text-primary dark:text-dark-primary text-3xl font-bold">
                                {{ $product->currency }} {{ number_format($product->price, 2) }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Stock Status -->
                <div class="mb-6">
                    <div class="flex items-center">
                        @if ($product->stock > 0)
                            <div class="flex items-center text-green-600">
                                <i class="ri-checkbox-circle-line mr-2"></i>
                                <span class="font-medium">In Stock ({{ $product->stock }} available)</span>
                            </div>
                        @else
                            <div class="flex items-center text-red-600">
                                <i class="ri-close-circle-line mr-2"></i>
                                <span class="font-medium">Out of Stock</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Product Description -->
                <div class="mb-8">
                    <h3 class="mb-3 text-lg font-semibold text-gray-900 dark:text-gray-100">Description</h3>
                    <div class="prose prose-gray dark:prose-invert max-w-none">
                        <p class="text-gray-700 dark:text-gray-300">{{ $product->description }}</p>
                    </div>
                </div>

                <!-- Purchase Actions -->
                <div class="mb-8 space-y-4">
                    @if ($product->stock > 0)
                        <!-- Quantity Selector -->
                        <div class="flex items-center space-x-4">
                            <label class="font-medium text-gray-700 dark:text-gray-300" for="quantity">Quantity:</label>
                            <div class="flex items-center overflow-hidden rounded-lg border border-gray-300 dark:border-gray-600">
                                <button class="quantity-decrease focus:ring-primary flex h-10 w-10 items-center justify-center bg-gray-100 transition-colors duration-200 hover:bg-gray-200 focus:outline-none focus:ring-2 dark:bg-gray-700 dark:hover:bg-gray-600" type="button" aria-label="Decrease quantity">
                                    <i class="ri-subtract-line text-gray-600 dark:text-gray-300"></i>
                                </button>
                                <input class="quantity-input h-10 w-16 border-0 bg-white text-center text-gray-900 focus:border-0 focus:ring-0 dark:bg-gray-800 dark:text-gray-100" id="quantity" type="number" value="1" min="1" max="{{ $product->stock }}">
                                <button class="quantity-increase focus:ring-primary flex h-10 w-10 items-center justify-center bg-gray-100 transition-colors duration-200 hover:bg-gray-200 focus:outline-none focus:ring-2 dark:bg-gray-700 dark:hover:bg-gray-600" type="button" aria-label="Increase quantity">
                                    <i class="ri-add-line text-gray-600 dark:text-gray-300"></i>
                                </button>
                            </div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Max: {{ $product->stock }}</span>
                        </div>

                        <!-- Purchase Buttons -->
                        <div class="flex space-x-4">
                            <button class="bg-primary hover:bg-accent flex-1 rounded-lg px-6 py-3 text-white transition-colors duration-200">
                                <i class="ri-shopping-cart-line mr-2"></i>
                                Add to Cart
                            </button>
                            <button class="bg-accent hover:bg-primary flex-1 rounded-lg px-6 py-3 text-white transition-colors duration-200">
                                <i class="ri-flashlight-line mr-2"></i>
                                Buy Now
                            </button>
                        </div>
                    @else
                        <div class="rounded-lg bg-red-50 p-4 text-red-800 dark:bg-red-900/20 dark:text-red-200">
                            <p class="font-medium">This product is currently out of stock.</p>
                        </div>
                    @endif

                    <!-- Wishlist Button -->
                    <button class="flex w-full items-center justify-center rounded-lg border border-gray-300 px-6 py-3 text-gray-700 transition-colors duration-200 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
                        <i class="ri-heart-line mr-2"></i>
                        Add to Wishlist
                    </button>
                </div>

                <!-- Product Files (if any) -->
                @if ($product->productFiles && $product->productFiles->count() > 0)
                    <div class="mb-8">
                        <h3 class="mb-3 text-lg font-semibold text-gray-900 dark:text-gray-100">Included Files</h3>
                        <div class="space-y-2">
                            @foreach ($product->productFiles as $file)
                                <div class="flex items-center rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                                    <i class="ri-file-line mr-3 text-gray-500"></i>
                                    <span class="text-gray-700 dark:text-gray-300">{{ $file->file_name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Product Specifications -->
                <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                    <h3 class="mb-3 text-lg font-semibold text-gray-900 dark:text-gray-100">Product Details</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">SKU:</span>
                            <span class="text-gray-900 dark:text-gray-100">{{ $product->slug }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Currency:</span>
                            <span class="text-gray-900 dark:text-gray-100">{{ $product->currency }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Created:</span>
                            <span class="text-gray-900 dark:text-gray-100">{{ $product->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Products -->
    @if ($relatedProducts->count() > 0)
        <section class="container mx-auto px-4 pb-12">
            <h2 class="mb-8 text-2xl font-bold text-gray-900 dark:text-gray-100">Related Products</h2>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($relatedProducts as $relatedProduct)
                    <x-shop.card-product :product="$relatedProduct" />
                @endforeach
            </div>
        </section>
    @endif

    <x-slot:javascript>
        <!-- Swiper JS -->
        <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Swiper components first
                let thumbsSwiper = null;
                let mainSwiper = null;

                // Check if there are multiple images for swiper
                const productImages = document.querySelectorAll('.product-main-swiper .swiper-slide');

                if (productImages.length > 1) {
                    // Initialize thumbnail swiper first
                    thumbsSwiper = new Swiper('.product-thumbs-swiper', {
                        spaceBetween: 10,
                        slidesPerView: 4,
                        freeMode: true,
                        watchSlidesProgress: true,
                        breakpoints: {
                            640: {
                                slidesPerView: 5,
                            },
                            768: {
                                slidesPerView: 6,
                            }
                        }
                    });

                    // Initialize main swiper
                    mainSwiper = new Swiper('.product-main-swiper', {
                        spaceBetween: 10,
                        navigation: {
                            nextEl: '.swiper-button-next',
                            prevEl: '.swiper-button-prev',
                        },
                        pagination: {
                            el: '.swiper-pagination',
                            clickable: true,
                        },
                        thumbs: {
                            swiper: thumbsSwiper,
                        },
                    });
                } else {
                    // Single image - just initialize basic swiper
                    mainSwiper = new Swiper('.product-main-swiper', {
                        spaceBetween: 10,
                        navigation: {
                            nextEl: '.swiper-button-next',
                            prevEl: '.swiper-button-prev',
                        },
                        pagination: {
                            el: '.swiper-pagination',
                            clickable: true,
                        }
                    });
                }

                if (quantityInput && decreaseBtn && increaseBtn) {
                    const maxStock = parseInt(quantityInput.getAttribute('max')) || 999;
                    const minQuantity = parseInt(quantityInput.getAttribute('min')) || 1;

                    // Function to update button states
                    function updateButtonStates() {
                        const currentValue = parseInt(quantityInput.value) || minQuantity;

                        // Update decrease button
                        if (currentValue <= minQuantity) {
                            decreaseBtn.disabled = true;
                            decreaseBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        } else {
                            decreaseBtn.disabled = false;
                            decreaseBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        }

                        // Update increase button
                        if (currentValue >= maxStock) {
                            increaseBtn.disabled = true;
                            increaseBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        } else {
                            increaseBtn.disabled = false;
                            increaseBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        }
                    }

                    // Decrease quantity function
                    function decreaseQuantity() {
                        let currentValue = parseInt(quantityInput.value) || minQuantity;
                        console.log('Current value:', currentValue);

                        if (currentValue > minQuantity) {
                            const newValue = currentValue - 1;
                            quantityInput.value = newValue;
                            console.log('Decreased to:', newValue);

                            // Update button states
                            updateButtonStates();

                            // Trigger change event
                            quantityInput.dispatchEvent(new Event('change'));
                        }
                    }

                    // Increase quantity function
                    function increaseQuantity() {
                        let currentValue = parseInt(quantityInput.value) || minQuantity;
                        console.log('Current value:', currentValue);

                        if (currentValue < maxStock) {
                            const newValue = currentValue + 1;
                            quantityInput.value = newValue;
                            console.log('Increased to:', newValue);

                            // Update button states
                            updateButtonStates();

                            // Trigger change event
                            quantityInput.dispatchEvent(new Event('change'));
                        }
                    }

                    // Bind events
                    decreaseBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        console.log('Decrease button clicked');
                        decreaseQuantity();
                    });

                    increaseBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        console.log('Increase button clicked');
                        increaseQuantity();
                    });

                    // Validate input directly
                    quantityInput.addEventListener('input', function(e) {
                        let value = parseInt(this.value);
                        console.log('Input changed to:', value);

                        if (isNaN(value) || value < minQuantity) {
                            this.value = minQuantity;
                        } else if (value > maxStock) {
                            this.value = maxStock;
                        }
                        updateButtonStates();
                    });

                    // Handle blur event to ensure valid value
                    quantityInput.addEventListener('blur', function(e) {
                        let value = parseInt(this.value);
                        if (isNaN(value) || value < minQuantity) {
                            this.value = minQuantity;
                        } else if (value > maxStock) {
                            this.value = maxStock;
                        }
                        updateButtonStates();
                    });

                    // Initialize button states
                    updateButtonStates();

                    console.log('Quantity controls initialized successfully');
                } else {
                    console.error('Quantity control elements not found');
                }
            });
        </script>
    </x-slot:javascript>
    </x-app-front-layout>
