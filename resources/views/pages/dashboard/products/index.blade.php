@section('title', $data['title'] ?? '')
@section('meta_description', '')

<x-app-layout>
    <section class="p-1 md:p-4">
        <div class="mb-3 flex items-center justify-between">
            <div>
                <h4 class="mb-0 text-2xl">Manage Products</h4>
                <p class="text-gray-500">Welcome back, {{ auth()->user()->name }}. Manage your products here.</p>
            </div>
            <div>
                <a class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" href="{{ route('admin.products.create') }}">
                    <i class="ri-add-line mr-2"></i>
                    Add Product
                </a>
            </div>
        </div>
    </section>

    <section class="p-1 md:p-4">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
            <x-card>
                <div class="flex items-center">
                    <div class="rounded-lg bg-blue-100 p-3">
                        <i class="ri-box-3-line text-xl text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Products</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center">
                    <div class="rounded-lg bg-green-100 p-3">
                        <i class="ri-price-tag-3-line text-xl text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">In Stock</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['in_stock'] }}</p>
                    </div>
                </div>
                <div class="mt-4 text-sm text-green-600">
                    <span class="ml-1">Available products</span>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center">
                    <div class="rounded-lg bg-yellow-100 p-3">
                        <i class="ri-error-warning-line text-xl text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Low Stock</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['low_stock'] }}</p>
                    </div>
                </div>
                <div class="mt-4 text-sm text-orange-600">
                    <span class="ml-1">≤5 items remaining</span>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center">
                    <div class="rounded-lg bg-red-100 p-3">
                        <i class="ri-close-circle-line text-xl text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Out of Stock</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['out_of_stock'] }}</p>
                    </div>
                </div>
                <div class="mt-4 text-sm text-red-600">
                    <span class="ml-1">Needs restocking</span>
                </div>
            </x-card>
        </div>
    </section>

    <section class="p-1 md:px-4">
        <x-card>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="border-b bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Owner</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($products as $product)
                            <tr class="transition-colors hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center">
                                        <img class="h-10 w-10 rounded-lg object-cover" src="{{ $product->thumbnail ? asset('storage/products/' . $product->thumbnail) : 'https://placehold.co/60x60/3b82f6/ffffff?text=' . substr($product->product_name, 0, 2) }}" alt="{{ $product->product_name }}">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $product->product_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $product->slug }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $product->user->name ?? 'N/A' }}</div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $product->currency }} {{ number_format($product->price, 2) }}
                                        @if ($product->discount_price && $product->discount_price < $product->price)
                                            <div class="text-xs text-green-600">
                                                Discount: {{ $product->currency }} {{ number_format($product->discount_price, 2) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    @if ($product->stock == 0)
                                        <span class="inline-flex rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-800">
                                            Out of stock
                                        </span>
                                    @elseif($product->stock <= 5)
                                        <span class="inline-flex rounded-full bg-yellow-100 px-2 py-1 text-xs font-semibold text-yellow-800">
                                            {{ $product->stock }} in stock
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">
                                            {{ $product->stock }} in stock
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    @if ($product->is_published)
                                        <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-800">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button class="p-1 text-blue-600 hover:text-blue-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="p-1 text-yellow-600 hover:text-yellow-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="p-1 text-red-600 hover:text-red-900" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-6 py-4 text-center text-gray-500" colspan="6">
                                    <div class="py-8">
                                        <i class="ri-box-3-line mb-4 text-4xl text-gray-400"></i>
                                        <p class="text-lg font-medium">No products found</p>
                                        <p class="text-sm">Start by creating your first product.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($products->hasPages())
                <div class="flex items-center justify-end border-t bg-gray-50 py-1">
                    <div class="flex items-center space-x-2">
                        {{ $products->links() }}
                    </div>
                </div>
            @else
                <div class="border-t bg-gray-50 px-6 py-4">
                    <div class="text-sm text-gray-700">
                        Showing {{ $products->count() }} {{ Str::plural('product', $products->count()) }}
                    </div>
                </div>
            @endif
        </x-card>
    </section>
</x-app-layout>
