<?php

declare(strict_types=1);

namespace App\Cart\Application\Controller\Api;

use App\Cart\Domain\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/cart/delete', methods: ['DELETE'])]
final class DeleteCartItemController extends AbstractController
{
    public function __construct(private readonly CartService $cartService)
    {
    }

    public function __invoke(Request $request): Response
    {
        $productId = $request->getPayload()->get('productId') ?? '';

        if (empty($productId)) {
            return $this->json([
                'success' => false,
                'message' => 'Invalid data',
            ]);
        }

        $this->cartService->removeFromCart($productId);

        return $this->json([
            'success' => true,
            'cart' => $this->cartService->getCartItems(),
        ]);
    }
}
