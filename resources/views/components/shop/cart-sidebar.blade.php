<!-- Shopping Cart Sidebar -->
<div class="fixed inset-0 z-50 hidden" id="cart-sidebar">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/50" id="cart-overlay"></div>

    <!-- Cart Panel -->
    <div class="absolute right-0 top-0 h-full w-full max-w-md translate-x-full transform bg-white shadow-xl transition-transform duration-300 ease-in-out dark:bg-gray-800" id="cart-panel">
        <!-- Cart Header -->
        <div class="flex items-center justify-between border-b border-gray-200 p-4 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Shopping Cart</h3>
            <button class="rounded-lg p-2 transition-colors duration-200 hover:bg-gray-100 dark:hover:bg-gray-700" id="close-cart">
                <i class="ri-close-line text-xl text-gray-500 dark:text-gray-400"></i>
            </button>
        </div>

        <!-- Cart Content -->
        <div class="flex h-full flex-col">
            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto p-4">
                <div class="space-y-4" id="cart-items">
                    <!-- Empty cart message -->
                    <div class="py-8 text-center" id="empty-cart">
                        <i class="ri-shopping-cart-line mb-4 text-4xl text-gray-400"></i>
                        <h4 class="mb-2 text-lg font-medium text-gray-900 dark:text-gray-100">Your cart is empty</h4>
                        <p class="mb-4 text-gray-500 dark:text-gray-400">Add some products to get started!</p>
                        <button class="bg-primary hover:bg-accent rounded-lg px-4 py-2 text-white transition-colors duration-200" id="continue-shopping">
                            Continue Shopping
                        </button>
                    </div>

                    <!-- Cart item template (hidden) -->
                    <div class="cart-item flex hidden items-center space-x-3 rounded-lg border border-gray-200 p-3 dark:border-gray-700" id="cart-item-template">
                        <img class="cart-item-image h-16 w-16 rounded-lg object-cover" src="" alt="">
                        <div class="flex-1">
                            <h4 class="cart-item-name text-sm font-medium text-gray-900 dark:text-gray-100"></h4>
                            <p class="cart-item-price text-primary text-sm font-semibold"></p>
                            <div class="mt-2 flex items-center">
                                <button class="cart-item-decrease flex h-6 w-6 items-center justify-center rounded-l border border-gray-300 text-xs hover:bg-gray-100">-</button>
                                <input class="cart-item-quantity h-6 w-12 border-b border-t border-gray-300 text-center text-xs" type="number" value="1" readonly>
                                <button class="cart-item-increase flex h-6 w-6 items-center justify-center rounded-r border border-gray-300 text-xs hover:bg-gray-100">+</button>
                            </div>
                        </div>
                        <button class="cart-item-remove p-1 text-red-500 transition-colors duration-200 hover:text-red-700">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="hidden space-y-4 border-t border-gray-200 p-4 dark:border-gray-700" id="cart-summary">
                <!-- Subtotal -->
                <div class="flex items-center justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                    <span class="font-semibold text-gray-900 dark:text-gray-100" id="cart-subtotal">$0.00</span>
                </div>

                <!-- Shipping -->
                <div class="flex items-center justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Shipping:</span>
                    <span class="font-medium text-green-600">Free</span>
                </div>

                <!-- Total -->
                <div class="flex items-center justify-between border-t border-gray-200 pt-2 text-lg font-bold dark:border-gray-700">
                    <span class="text-gray-900 dark:text-gray-100">Total:</span>
                    <span class="text-primary" id="cart-total">$0.00</span>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-2">
                    <button class="bg-primary hover:bg-accent w-full rounded-lg py-3 font-medium text-white transition-colors duration-200">
                        Proceed to Checkout
                    </button>
                    <button class="w-full rounded-lg border border-gray-300 py-2 text-gray-700 transition-colors duration-200 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                        View Cart
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cartSidebar = document.getElementById('cart-sidebar');
        const cartPanel = document.getElementById('cart-panel');
        const cartOverlay = document.getElementById('cart-overlay');
        const closeCart = document.getElementById('close-cart');
        const cartToggle = document.getElementById('cart-toggle');
        const continueShoppingBtn = document.getElementById('continue-shopping');

        // Cart data
        let cartItems = [];

        // Open cart
        function openCart() {
            cartSidebar.classList.remove('hidden');
            setTimeout(() => {
                cartPanel.classList.remove('translate-x-full');
            }, 10);
        }

        // Close cart
        function closeCartSidebar() {
            cartPanel.classList.add('translate-x-full');
            setTimeout(() => {
                cartSidebar.classList.add('hidden');
            }, 300);
        }

        // Event listeners
        if (cartToggle) {
            cartToggle.addEventListener('click', openCart);
        }

        if (closeCart) {
            closeCart.addEventListener('click', closeCartSidebar);
        }

        if (cartOverlay) {
            cartOverlay.addEventListener('click', closeCartSidebar);
        }

        if (continueShoppingBtn) {
            continueShoppingBtn.addEventListener('click', closeCartSidebar);
        }

        // Add to cart function
        window.addToCartSidebar = function(productData) {
            const existingItem = cartItems.find(item => item.id === productData.id);
            console.log(productData);

            if (existingItem) {
                existingItem.quantity += productData.quantity || 1;
            } else {
                cartItems.push({
                    id: productData.id,
                    name: productData.name,
                    price: productData.price,
                    image: productData.image,
                    quantity: productData.quantity || 1
                });
            }

            updateCartDisplay();
            openCart();
        };

        // Update cart display
        function updateCartDisplay() {
            const cartItemsContainer = document.getElementById('cart-items');
            const emptyCart = document.getElementById('empty-cart');
            const cartSummary = document.getElementById('cart-summary');
            const cartCount = document.querySelector('.cart-count');

            // Clear existing items
            const existingItems = cartItemsContainer.querySelectorAll('.cart-item:not(#cart-item-template)');
            existingItems.forEach(item => item.remove());

            if (cartItems.length === 0) {
                emptyCart.classList.remove('hidden');
                cartSummary.classList.add('hidden');
                if (cartCount) cartCount.textContent = '0';
                return;
            }

            emptyCart.classList.add('hidden');
            cartSummary.classList.remove('hidden');

            let total = 0;
            const template = document.getElementById('cart-item-template');

            cartItems.forEach((item, index) => {
                const itemElement = template.cloneNode(true);
                itemElement.id = `cart-item-${item.id}`;
                itemElement.classList.remove('hidden');

                itemElement.querySelector('.cart-item-image').src = item.image;
                itemElement.querySelector('.cart-item-name').textContent = item.name;
                itemElement.querySelector('.cart-item-price').textContent = `$${item.price}`;
                itemElement.querySelector('.cart-item-quantity').value = item.quantity;

                // Quantity controls
                itemElement.querySelector('.cart-item-decrease').addEventListener('click', () => {
                    if (item.quantity > 1) {
                        item.quantity--;
                        updateCartDisplay();
                    }
                });

                itemElement.querySelector('.cart-item-increase').addEventListener('click', () => {
                    item.quantity++;
                    updateCartDisplay();
                });

                // Remove item
                itemElement.querySelector('.cart-item-remove').addEventListener('click', () => {
                    cartItems.splice(index, 1);
                    updateCartDisplay();
                });

                cartItemsContainer.appendChild(itemElement);
                total += item.price * item.quantity;
            });

            // Update totals
            document.getElementById('cart-subtotal').textContent = `$${total.toFixed(2)}`;
            document.getElementById('cart-total').textContent = `$${total.toFixed(2)}`;

            // Update cart count
            const totalItems = cartItems.reduce((sum, item) => sum + item.quantity, 0);
            if (cartCount) cartCount.textContent = totalItems;
        }

        // Initialize cart display
        updateCartDisplay();
    });
</script>
