<?php

declare(strict_types=1);

namespace App\Cart\Application\Controller\Api;

use App\Cart\Application\UseCase\Update\UpdateCartCommand;
use App\Cart\Application\UseCase\Update\UpdateCartCommandHandler;
use App\Cart\Domain\Service\CartService;
use App\Common\Application\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/cart/update', methods: ['PATCH'])]
final class UpdateCartController extends AbstractController
{
    public function __construct(
        private readonly CartService              $cartService,
        private readonly UpdateCartCommandHandler $updateCartCommandHandler,
    )
    {
    }

    public function __invoke(
        #[MapRequestPayload] UpdateCartCommand $updateCartCommand,
    ): Response
    {
        $this->updateCartCommandHandler->handle($updateCartCommand);

        return $this->singleObjectResponse(['cart' => $this->cartService->getCartItems()], Response::HTTP_OK);
    }
}
