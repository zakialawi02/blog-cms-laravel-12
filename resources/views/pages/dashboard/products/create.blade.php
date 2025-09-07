@section('title', $data['title'] ?? '')
@section('meta_description', '')

<x-app-layout>
    <section class="p-1 md:p-4">
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="mb-0 text-2xl font-semibold text-gray-800">Create New Product</h4>
                    <p class="text-gray-600">Add a new product to your inventory</p>
                </div>
                <div>
                    <a class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" href="{{ route('admin.products.index') }}">
                        <i class="ri-arrow-left-line mr-2"></i>
                        Back to Products
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="p-1 md:p-4">
        <x-card>
            <form class="space-y-6" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Product Name -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <x-dashboard.input-label for="product_name" value="Product Name" />
                        <x-dashboard.text-input class="mt-1 block w-full" id="product_name" name="product_name" type="text" :value="old('product_name')" placeholder="Enter product name" required autofocus />
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('product_name')" />
                    </div>

                    <!-- Slug -->
                    <div>
                        <x-dashboard.input-label for="slug" value="Slug" />
                        <x-dashboard.text-input class="mt-1 block w-full" id="slug" name="slug" type="text" :value="old('slug')" placeholder="Auto-generated from product name" />
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('slug')" />
                        <p class="mt-1 text-sm text-gray-500">Leave empty to auto-generate from product name</p>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <x-dashboard.input-label for="description" value="Description" />
                    <x-dashboard.textarea-input class="mt-1 block w-full" id="description" name="description" rows="4" placeholder="Enter product description" required>{{ old('description') }}</x-dashboard.textarea-input>
                    <x-dashboard.input-error class="mt-2" :messages="$errors->get('description')" />
                </div>

                <!-- Price and Currency -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div>
                        <x-dashboard.input-label for="currency" value="Currency" />
                        <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" id="currency" name="currency">
                            @foreach ($currencies as $code => $name)
                                <option value="{{ $code }}" {{ old('currency', 'USD') === $code ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('currency')" />
                    </div>

                    <div>
                        <x-dashboard.input-label for="price" value="Price" />
                        <x-dashboard.text-input class="mt-1 block w-full" id="price" name="price" type="number" step="0.01" min="0" :value="old('price')" placeholder="0.00" required />
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('price')" />
                    </div>

                    <div>
                        <x-dashboard.input-label for="discount_price" value="Discount Price (Optional)" />
                        <x-dashboard.text-input class="mt-1 block w-full" id="discount_price" name="discount_price" type="number" step="0.01" min="0" :value="old('discount_price')" placeholder="0.00" />
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('discount_price')" />
                    </div>
                </div>

                <!-- Stock -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <x-dashboard.input-label for="stock" value="Stock Quantity" />
                        <x-dashboard.text-input class="mt-1 block w-full" id="stock" name="stock" type="number" min="0" :value="old('stock', 0)" required />
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('stock')" />
                    </div>

                    <!-- Thumbnail -->
                    <div>
                        <x-dashboard.input-label for="thumbnail" value="Product Image" />
                        <input class="mt-1 block w-full pl-5 text-sm text-gray-500 file:mr-4 file:rounded-full file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100" id="thumbnail" name="thumbnail" type="file" accept="image/*" />
                        <x-dashboard.input-error class="mt-2" :messages="$errors->get('thumbnail')" />
                        <p class="mt-1 text-sm text-gray-500">Max size: 2MB. Formats: JPG, JPEG, PNG, GIF</p>
                    </div>
                </div>

                <!-- Publication Status -->
                <div class="flex items-center">
                    <input class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" id="is_published" name="is_published" type="checkbox" value="1" {{ old('is_published') ? 'checked' : '' }}>
                    <label class="ml-2 block text-sm text-gray-900" for="is_published">
                        Publish this product immediately
                    </label>
                </div>
                <x-dashboard.input-error class="mt-2" :messages="$errors->get('is_published')" />

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 border-t pt-6">
                    <a class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" href="{{ route('admin.products.index') }}">
                        Cancel
                    </a>
                    <x-dashboard.primary-button type="submit">
                        <i class="ri-save-line mr-2"></i>
                        Create Product
                    </x-dashboard.primary-button>
                </div>
            </form>
        </x-card>
    </section>

    @push('scripts')
        <script>
            // Auto-generate slug from product name
            document.getElementById('product_name').addEventListener('input', function(e) {
                const slug = e.target.value
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim('-');

                const slugInput = document.getElementById('slug');
                if (slugInput.value === '' || slugInput.dataset.autoGenerated === 'true') {
                    slugInput.value = slug;
                    slugInput.dataset.autoGenerated = 'true';
                }
            });

            // Mark slug as manually edited if user types in it
            document.getElementById('slug').addEventListener('input', function(e) {
                if (e.target.value !== '') {
                    e.target.dataset.autoGenerated = 'false';
                }
            });

            // Validate discount price is less than regular price
            document.getElementById('discount_price').addEventListener('input', function(e) {
                const price = parseFloat(document.getElementById('price').value) || 0;
                const discountPrice = parseFloat(e.target.value) || 0;

                if (discountPrice > 0 && discountPrice >= price) {
                    e.target.setCustomValidity('Discount price must be less than regular price');
                } else {
                    e.target.setCustomValidity('');
                }
            });

            document.getElementById('price').addEventListener('input', function(e) {
                const price = parseFloat(e.target.value) || 0;
                const discountPriceInput = document.getElementById('discount_price');
                const discountPrice = parseFloat(discountPriceInput.value) || 0;

                if (discountPrice > 0 && discountPrice >= price) {
                    discountPriceInput.setCustomValidity('Discount price must be less than regular price');
                } else {
                    discountPriceInput.setCustomValidity('');
                }
            });
        </script>
    @endpush
</x-app-layout>
