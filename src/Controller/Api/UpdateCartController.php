<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\CartService;
use App\Service\ProductsProviderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/cart/update', methods: ['PATCH'])]
final class UpdateCartController extends AbstractController
{
    private array $products;

    public function __construct(
        private readonly CartService $cartService,
        ProductsProviderService      $productsProviderService,
    )
    {
        $this->products = $productsProviderService->getProducts();
    }

    public function __invoke(Request $request): Response
    {
        $productId = $request->getPayload()->get('product_id') ?? '';
        $quantity = (int)($request->getPayload()->get('quantity') ?? 0);

        if (empty($productId) || ($quantity < 0 || $quantity > 999) || (!isset($this->products[$productId]) && $quantity > 0)) {
            return $this->json(['success' => false, 'message' => 'Invalid data']);
        }

        $this->cartService->updateQuantity($productId, $quantity);

        return $this->json(['success' => true, 'cart' => $this->cartService->getCartItems()]);
    }
}
