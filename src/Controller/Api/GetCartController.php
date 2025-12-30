<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/cart', methods: ['GET'])]
final class GetCartController extends AbstractController
{
    public function __construct(
        private readonly CartService $cartService,
    )
    {
    }

    public function __invoke(): Response
    {
        return $this->json(['success' => true, 'cart' => $this->cartService->getCartItems()]);
    }


}
