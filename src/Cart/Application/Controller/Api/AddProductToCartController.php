<?php

declare(strict_types=1);

namespace App\Cart\Application\Controller\Api;

use App\Cart\Domain\Service\CartService;
use App\Common\Application\Controller\AbstractController;
use App\Product\Application\Dto\ProductDto;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/cart/add', methods: ['POST'])]
class AddProductToCartController extends AbstractController
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
        $productId = $productDto->productId;
        $quantity = (int)$productDto->quantity;

        $this->cartService->addToCart($productId, $quantity);

        return $this->singleObjectResponse(null, Response::HTTP_OK);
    }
}
