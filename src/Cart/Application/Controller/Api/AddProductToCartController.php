<?php

declare(strict_types=1);

namespace App\Cart\Application\Controller\Api;

use App\Cart\Domain\Service\CartService;
use App\Product\Infrastructure\Service\ProductsProviderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/cart/add', methods: ['POST'])]
class AddProductToCartController extends AbstractController
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
        $productId = $request->request->get('product_id', '');
        $quantity = (int)($request->request->get('quantity', 1));

        if (empty($productId) || $quantity < 1 || $quantity > 999 || !isset($this->products[$productId])) {
            return $this->json(['success' => false, 'message' => 'Invalid data']);
        }

        $this->cartService->addToCart($productId, $quantity);

        return $this->json(['success' => true, 'cart' => $this->cartService->getCartItems()]);
    }
}
