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
            firstName: "",
            lastName: "",
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
                const product = this.products.find(p => p.id === productId);
                if (!product) return;

                const item = this.cart.find(item => item.id === productId);
                let isNewItem = false;
                let oldQuantity = 0;
                let oldTotal = 0;

                if (item) {
                    oldQuantity = item.quantity;
                    oldTotal = item.total;
                    item.quantity += quantity;
                    item.total = item.price * item.quantity;
                } else {
                    isNewItem = true;
                    this.cart.push({
                        id: productId,
                        name: product.name,
                        price: product.price,
                        quantity: quantity,
                        total: product.price * quantity,
                    });
                }
                this.calculateTotal();
                this.showCart = true;

                try {
                    const response = await axios.post(
                        "/api/v1/cart",
                        {
                            productId: String(productId),
                            quantity: quantity,
                        },
                        {withCredentials: true},
                    );
                    const result = response.data;
                    if (!result.result) {
                        if (isNewItem) {
                            this.cart = this.cart.filter(cartItem => cartItem.id !== productId);
                        } else {
                            item.quantity = oldQuantity;
                            item.total = oldTotal;
                        }
                        this.calculateTotal();
                        this.message = result.message;
                    }
                } catch (error) {
                    if (isNewItem) {
                        this.cart = this.cart.filter(cartItem => cartItem.id !== productId);
                    } else {
                        item.quantity = oldQuantity;
                        item.total = oldTotal;
                    }
                    this.calculateTotal();
                    this.message = "Error adding to cart";
                }
            },

            async update(productId, quantity) {
                quantity = parseInt(quantity);
                if (quantity < 0 || quantity > 9999) return;

                const item = this.cart.find(item => item.id === productId);
                if (!item) return;

                const oldQuantity = item.quantity;
                const oldTotal = item.total;

                item.quantity = quantity;
                item.total = item.price * quantity;
                this.calculateTotal();

                try {
                    const response = await axios.patch(
                        "/api/v1/cart",
                        {
                            productId: String(productId),
                            quantity: String(quantity),
                        },
                        {withCredentials: true},
                    );

                    if (response.status === 204) {
                        if (quantity === 0) {
                            this.cart = this.cart.filter(cartItem => cartItem.id !== productId);
                        }
                    } else {
                        item.quantity = oldQuantity;
                        item.total = oldTotal;
                        this.calculateTotal();
                        this.message = response.data.message || "Error updating cart";
                    }
                } catch (error) {
                    item.quantity = oldQuantity;
                    item.total = oldTotal;
                    this.calculateTotal();
                    this.message = "Error updating cart";
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
                    !this.firstName ||
                    !this.lastName ||
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
                    const response = await axios.post(
                        "/api/v1/checkout",
                        {
                            email: this.email,
                            phone: this.phone,
                            firstName: this.firstName,
                            lastName: this.lastName,
                            cardNumber: parseInt(this.cardNumber),
                            cardExpiryMonth: parseInt(this.cardExpiryMonth),
                            cardExpiryYear: parseInt(this.cardExpiryYear),
                            cardCvv: parseInt(this.cardCvv),
                            cardHolderName: this.cardHolderName,
                        },
                        {withCredentials: true},
                    );
                    const result = response.data;
                    this.message = result.message || "";
                    if (result.result) {
                        this.cart = [];
                        this.total = 0;
                        this.email = "";
                        this.phone = "";
                        this.firstName = "";
                        this.lastName = "";
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
