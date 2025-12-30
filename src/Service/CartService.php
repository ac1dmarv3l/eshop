<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class CartService
{
    private const string CART_SESSION_KEY = 'cart';

    private array $products;

    public function __construct(
        private RequestStack $requestStack,
        ProductsProviderService $productsProviderService,
    )
    {
        $this->products = $productsProviderService->getProducts();
    }

    public function getCartItems(): array
    {
        $cart = $this->getCart();

        $items = [];
        foreach ($cart as $productId => $quantity) {
            if (isset($this->products[$productId])) {
                $items[] = [
                    'id' => $productId,
                    'name' => $this->products[$productId]['name'],
                    'price' => $this->products[$productId]['price'],
                    'quantity' => $quantity,
                    'total' => $this->products[$productId]['price'] * $quantity,
                ];
            }
        }

        return $items;
    }

    public function updateQuantity(string $productId, int $quantity): void
    {
        $cart = $this->getCart();

        if ($quantity === 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $quantity;
        }

        $this->requestStack->getSession()->set(self::CART_SESSION_KEY, $cart);
    }

    public function getCart(): array
    {
        return $this->requestStack->getSession()->get(self::CART_SESSION_KEY, []);
    }

    public function addToCart(string $productId, int $quantity): void
    {
        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }

        $this->requestStack->getSession()->set(self::CART_SESSION_KEY, $cart);
    }

    public function removeFromCart(string $productId): void
    {
        $cart = $this->getCart();

        unset($cart[$productId]);

        $this->requestStack->getSession()->set(self::CART_SESSION_KEY, $cart);
    }

    public function clearCart(): void
    {
        $this->requestStack->getSession()->remove(self::CART_SESSION_KEY);
    }
}
