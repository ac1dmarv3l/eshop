<?php

declare(strict_types=1);

namespace App\Cart\Domain\Service;

use App\Cart\Application\Exception\CartException;
use App\Product\Infrastructure\Service\ProductsProviderService;
use Symfony\Component\HttpFoundation\RequestStack;

final class CartService
{
    private const string CART_SESSION_KEY = 'cart';

    private array $products;

    public function __construct(
        private readonly RequestStack $requestStack,
        ProductsProviderService       $productsProviderService,
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

    public function updateQuantity(string $productId, string $quantity): void
    {
        if ((!is_numeric($quantity) || (int)$quantity < 0 || (int)$quantity > 999) || (!isset($this->products[$productId]) && (int)$quantity > 0)) {
            throw CartException::fromString('Invalid data');
        }

        $cart = $this->getCart();

        if ((int)$quantity === 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = (int)$quantity;
        }

        $this->requestStack->getSession()->set(self::CART_SESSION_KEY, $cart);
    }

    public function getCart(): array
    {
        return $this->requestStack->getSession()->get(self::CART_SESSION_KEY, []);
    }

    public function addToCart(string $productId, int $quantity): void
    {
        if (
            empty($productId) ||
            $quantity < 1 ||
            $quantity > 999 ||
            !isset($this->products[$productId])
        ) {
            throw CartException::fromString('Invalid data');
        }

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
