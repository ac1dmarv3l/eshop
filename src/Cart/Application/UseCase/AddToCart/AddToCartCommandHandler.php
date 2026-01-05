<?php

declare(strict_types=1);

namespace App\Cart\Application\UseCase\AddToCart;

use App\Cart\Domain\Service\CartService;

final readonly class AddToCartCommandHandler
{
    public function __construct(
        private CartService $cartService,
    )
    {
    }

    public function handle(AddToCartCommand $command): void
    {
        $this->cartService->addToCart($command->productId, $command->quantity);
    }
}
