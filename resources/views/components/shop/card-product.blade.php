@props(['product'])

<article {{ $attributes->merge(['class' => 'flex flex-col bg-white dark:bg-dark-base-200 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300']) }}>
    <div class="relative">
        @if ($product->productImages && $product->productImages->count() > 0)
            <img class="h-64 w-full rounded-t-lg object-cover object-center lg:h-72" src="{{ asset('storage/products/' . $product->productImages->first()->image_path) }}" alt="{{ $product->product_name }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('assets/img/image-placeholder.png') }}'">
        @elseif($product->thumbnail)
            <img class="h-64 w-full rounded-t-lg object-cover object-center lg:h-72" src="{{ asset('storage/products/' . $product->thumbnail) }}" alt="{{ $product->product_name }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('assets/img/image-placeholder.png') }}'">
        @else
            <img class="h-64 w-full rounded-t-lg object-cover object-center lg:h-72" src="{{ asset('assets/img/image-placeholder.png') }}" alt="{{ $product->product_name }}" loading="lazy">
        @endif

        @if ($product->discount_price && $product->discount_price < $product->price)
            <div class="absolute left-2 top-2">
                <span class="rounded-full bg-red-500 px-2 py-1 text-sm font-semibold text-white">
                    @php
                        $discountPercent = round((($product->price - $product->discount_price) / $product->price) * 100);
                    @endphp
                    -{{ $discountPercent }}%
                </span>
            </div>
        @endif
    </div>

    <div class="flex flex-grow flex-col p-3">
        <h2 class="text-dark dark:text-dark-light hover:text-muted dark:hover:text-dark-muted mt-1 line-clamp-2 text-xl font-semibold hover:underline">
            <a href="{{ route('shop.product.show', $product->slug) }}">
                {{ $product->product_name }}
            </a>
        </h2>

        <p class="text-muted dark:text-dark-muted mt-2 line-clamp-3 flex-grow">
            {{ $product->description }}
        </p>

        <div class="mt-4 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                @if ($product->discount_price && $product->discount_price < $product->price)
                    <span class="text-primary dark:text-dark-primary text-xl font-bold" data-price="{{ $product->discount_price }}">
                        {{ $product->currency }} {{ number_format($product->discount_price, 2) }}
                    </span>
                    <span class="text-gray-500 line-through" data-price="{{ $product->price }}">
                        {{ $product->currency }} {{ number_format($product->price, 2) }}
                    </span>
                @else
                    <span class="text-primary dark:text-dark-primary text-md font-bold" data-price="{{ $product->price }}">
                        {{ $product->currency }} {{ number_format($product->price, 2) }}
                    </span>
                @endif
            </div>

            <div class="text-muted dark:text-dark-muted text-sm">
                Stock: {{ $product->stock }}
            </div>
        </div>

        <div class="mt-4 flex items-center justify-between">
            <div class="text-muted dark:text-dark-muted flex items-center text-sm">
                <i class="ri-user-line mr-1"></i>
                <span title="{{ $product->user->name }}">{{ Str::limit($product->user->name, 14) }}</span>
            </div>

            <div class="flex space-x-2">
                <a class="bg-secondary hover:bg-primary flex items-center rounded-lg px-2.5 py-1.5 text-white transition-colors duration-200" href="{{ route('shop.product.show', $product->slug) }}">
                    <i class="ri-eye-line mr-1"></i>
                    View
                </a>
                <button class="bg-primary hover:bg-accent flex items-center rounded-lg px-2.5 py-1.5 text-white transition-colors duration-200">
                    <i class="ri-shopping-cart-line mr-1"></i>
                    Cart
                </button>
            </div>
        </div>
    </div>
</article>
