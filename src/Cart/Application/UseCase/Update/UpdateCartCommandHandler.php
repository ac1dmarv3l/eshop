<?php

declare(strict_types=1);

namespace App\Cart\Application\UseCase\Update;

use App\Cart\Domain\Service\CartService;

final readonly class UpdateCartCommandHandler
{
    public function __construct(
        private CartService $cartService,
    )
    {
    }

    public function handle(UpdateCartCommand $updateCartCommand): void
    {
        $this->cartService->updateQuantity($updateCartCommand->productId, $updateCartCommand->quantity);
    }
}
