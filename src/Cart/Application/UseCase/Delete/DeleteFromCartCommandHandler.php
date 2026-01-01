<?php

declare(strict_types=1);

namespace App\Cart\Application\UseCase\Delete;

use App\Cart\Domain\Service\CartService;

final readonly class DeleteFromCartCommandHandler
{
    public function __construct(
        private CartService $cartService,
    )
    {

    }

    public function handle(DeleteFromCartCommand $deleteFromCartCommand): void
    {
        $this->cartService->removeFromCart($deleteFromCartCommand->productId);
    }
}
