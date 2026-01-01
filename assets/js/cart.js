"use strict";

export default function (Alpine, axios) {
    document.addEventListener("alpine:init", () => {
        Alpine.data("cart", () => ({
            showCart: false,
            cart: [],
            loading: false,
            message: "",
            total: 0,
            products: [],
            quantities: {},
            showOrderForm: false,
            email: "",
            phone: "",
            cardNumber: "",
            cardExpiryMonth: "",
            cardExpiryYear: "",
            cardCvv: "",
            cardHolderName: "",

            init() {
                this.loadProducts();
                this.loadCart();
            },

            async loadCart() {
                try {
                    const response = await axios.get("/api/cart", {
                        withCredentials: true,
                    });
                    const result = response.data;
                    if (result.result) {
                        this.cart = result.cart;
                        this.calculateTotal();
                    }
                } catch (error) {
                    console.error("Error loading cart:");
                }
            },

            async loadProducts() {
                try {
                    const response = await axios.get("/api/products");
                    const result = await response.data;
                    if (result.success) {
                        this.products = result.products;
                    }
                } catch (error) {
                    console.error("Error loading products:");
                }
            },

            calculateTotal() {
                this.total = this.cart.reduce(
                    (sum, item) => sum + item.total,
                    0,
                );
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
                    const response = await axios.post(
                        "/api/cart/add",
                        {
                            productId: String(productId),
                            quantity: String(quantity),
                        },
                        { withCredentials: true },
                    );
                    const result = response.data;
                    if (result.result) {
                        await this.loadCart();
                        this.calculateTotal();
                        this.showCart = true;
                    } else {
                        // the message is not sent by backend by now so it's not handling
                        this.message = result.message;
                    }
                } catch (error) {
                    this.message = "Error adding to cart";
                }
            },

            async updateQuantity(productId, quantity) {
                quantity = parseInt(quantity);
                if (quantity < 1 || quantity > 999) return;
                try {
                    const response = await axios.patch(
                        "/api/cart/update",
                        {
                            productId: String(productId),
                            quantity: String(quantity),
                        },
                        { withCredentials: true },
                    );
                    const result = response.data;
                    if (result.result) {
                        this.cart = result.cart;
                        this.calculateTotal();
                    } else {
                        this.message = result.message;
                    }
                } catch (error) {
                    this.message = "Error updating quantity";
                }
            },

            async deleteItem(productId) {
                try {
                    const response = await axios.delete("/api/cart", {
                        data: { productId: String(productId) },
                        withCredentials: true,
                    });
                    const result = response.data;
                    if (result.result) {
                        await this.loadCart();
                        this.calculateTotal();
                    } else {
                        this.message = result.message;
                    }
                } catch (error) {
                    this.message = "Error deleting item";
                }
            },

            async checkout() {
                if (this.cart.length === 0) {
                    this.message = "Cart is empty";
                    return;
                }

                if (
                    !this.email ||
                    !this.phone ||
                    !this.cardNumber ||
                    !this.cardExpiryMonth ||
                    !this.cardExpiryYear ||
                    !this.cardCvv ||
                    !this.cardHolderName
                ) {
                    this.message = "Please fill in all required fields";
                    return;
                }

                this.loading = true;
                this.message = "";

                try {
                    const response = await fetch("/api/cart/checkout", {
                        method: "POST",
                        credentials: "include",
                        body: new URLSearchParams({
                            email: this.email,
                            phone: this.phone,
                            cardNumber: this.cardNumber,
                            cardExpiryMonth: this.cardExpiryMonth,
                            cardExpiryYear: this.cardExpiryYear,
                            cardCvv: this.cardCvv,
                            cardHolderName: this.cardHolderName,
                        }),
                    });
                    const result = await response.json();
                    this.message = result.message;
                    if (result.success) {
                        this.cart = [];
                        this.total = 0;
                        this.email = "";
                        this.phone = "";
                        this.cardNumber = "";
                        this.cardExpiryMonth = "";
                        this.cardExpiryYear = "";
                        this.cardCvv = "";
                        this.cardHolderName = "";
                        this.showCart = false;
                    }
                } catch (error) {
                    this.message = "Error during checkout";
                } finally {
                    this.loading = false;
                }
            },
        }));
    });
}
