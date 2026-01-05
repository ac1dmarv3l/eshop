<?php

declare(strict_types=1);

namespace App\Cart\Application\UseCase\Get;

use App\Cart\Domain\Service\CartService;

final readonly class GetCartQueryHandler
{
    public function __construct(
        private CartService $cartService
    )
    {
    }

    public function handle(): array
    {
        return $this->cartService->getCartItems();
    }
}
