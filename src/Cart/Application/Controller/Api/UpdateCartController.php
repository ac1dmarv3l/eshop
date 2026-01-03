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

#[Route(path: '/api/v1/cart', methods: ['PATCH'])]
final class UpdateCartController extends AbstractController
{
    public function __construct(
        private readonly UpdateCartCommandHandler $updateCartCommandHandler,
    )
    {
    }

    public function __invoke(
        #[MapRequestPayload] UpdateCartCommand $updateCartCommand,
    ): Response
    {
        $this->updateCartCommandHandler->handle($updateCartCommand);

        return $this->singleObjectResponse(null, Response::HTTP_NO_CONTENT);
    }
}
