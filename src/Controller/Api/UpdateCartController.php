<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Dto\ProductDto;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/cart/update', methods: ['PATCH'])]
final class UpdateCartController extends AbstractController
{
    public function __construct(
        private readonly CartService $cartService,
    )
    {
    }

    public function __invoke(
        #[MapRequestPayload] ProductDto $productDto,
    ): Response
    {
        $this->cartService->updateQuantity($productDto->productId, $productDto->quantity);

        return $this->json(['success' => true, 'cart' => $this->cartService->getCartItems()]);
    }
}
