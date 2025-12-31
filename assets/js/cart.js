'use strict';

export default function (Alpine) {
    document.addEventListener('alpine:init', () => {
        Alpine.data('cart', () => ({
            showCart: false,
            cart: [],
            loading: false,
            message: '',
            total: 0,
            products: [],
            quantities: {},
            showOrderForm: false,
            email: '',
            phone: '',
            cardNumber: '',
            cardExpiryMonth: '',
            cardExpiryYear: '',
            cardCvv: '',
            cardHolderName: '',

            init() {
                this.loadProducts();
                this.loadCart();
            },

            async loadCart() {
                try {
                    const response = await fetch('/api/cart', {
                        method: 'GET',
                        credentials: 'include',
                    });
                    const result = await response.json();
                    if (result.success) {
                        this.cart = result.cart;
                        this.calculateTotal();
                    }
                } catch (error) {
                    console.error('Error loading cart:', error);
                }
            },

            async loadProducts() {
                try {
                    const response = await fetch('/api/products', {
                        method: 'GET',
                    });
                    const result = await response.json();
                    if (result.success) {
                        this.products = result.products;
                    }
                } catch (error) {
                    console.error('Error loading products:', error);
                }
            },

            calculateTotal() {
                this.total = this.cart.reduce((sum, item) => sum + item.total, 0);
            },

            getQuantity(productId) {
                return this.quantities[productId] || 1;
            },

            setQuantity(productId, quantity) {
                this.quantities[productId] = quantity;
            },

            async addToCart(productId) {
                const quantity = this.getQuantity(productId);
                try {
                    const response = await fetch('/api/cart/add', {
                        method: 'POST',
                        credentials: 'include',
                        body: new URLSearchParams({ product_id: productId, quantity: quantity })
                    });
                    const result = await response.json();
                    if (result.success) {
                        this.cart = result.cart;
                        this.calculateTotal();
                        this.showCart = true;
                    } else {
                        this.message = result.message;
                    }
                } catch (error) {
                    this.message = 'Error adding to cart';
                }
            },

            async updateQuantity(productId, quantity) {
                quantity = parseInt(quantity);
                if (quantity < 1 || quantity > 999) return;
                try {
                    const response = await fetch('/api/cart/update', {
                        method: 'PATCH',
                        credentials: 'include',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ productId: productId, quantity: quantity.toString() })
                    });
                    const result = await response.json();
                    if (result.success) {
                        this.cart = result.cart;
                        this.calculateTotal();
                    } else {
                        this.message = result.message;
                    }
                } catch (error) {
                    this.message = 'Error updating quantity';
                }
            },

            async deleteItem(productId) {
                try {
                    const response = await fetch('/api/cart/delete', {
                        method: 'DELETE',
                        credentials: 'include',
                        body: new URLSearchParams({ product_id: productId })
                    });
                    const result = await response.json();
                    if (result.success) {
                        this.cart = result.cart;
                        this.calculateTotal();
                    } else {
                        this.message = result.message;
                    }
                } catch (error) {
                    this.message = 'Error deleting item';
                }
            },

            async checkout() {
                if (this.cart.length === 0) {
                    this.message = 'Cart is empty';
                    return;
                }

                if (!this.email || !this.phone || !this.cardNumber || !this.cardExpiryMonth || !this.cardExpiryYear || !this.cardCvv || !this.cardHolderName) {
                    this.message = 'Please fill in all required fields';
                    return;
                }

                this.loading = true;
                this.message = '';

                try {
                    const response = await fetch('/api/cart/checkout', {
                        method: 'POST',
                        credentials: 'include',
                        body: new URLSearchParams({
                            email: this.email,
                            phone: this.phone,
                            cardNumber: this.cardNumber,
                            cardExpiryMonth: this.cardExpiryMonth,
                            cardExpiryYear: this.cardExpiryYear,
                            cardCvv: this.cardCvv,
                            cardHolderName: this.cardHolderName,
                        })
                    });
                    const result = await response.json();
                    this.message = result.message;
                    if (result.success) {
                        this.cart = [];
                        this.total = 0;
                        this.email = '';
                        this.phone = '';
                        this.cardNumber = '';
                        this.cardExpiryMonth = '';
                        this.cardExpiryYear = '';
                        this.cardCvv = '';
                        this.cardHolderName = '';
                        this.showCart = false;
                    }
                } catch (error) {
                    this.message = 'Error during checkout';
                } finally {
                    this.loading = false;
                }
            }
        }));
    });
}
