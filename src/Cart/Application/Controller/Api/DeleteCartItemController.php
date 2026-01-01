<?php

declare(strict_types=1);

namespace App\Cart\Application\Controller\Api;

use App\Cart\Application\UseCase\Delete\DeleteFromCartCommand;
use App\Cart\Application\UseCase\Delete\DeleteFromCartCommandHandler;
use App\Common\Application\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/cart', methods: ['DELETE'])]
final class DeleteCartItemController extends AbstractController
{
    public function __construct(
        private readonly DeleteFromCartCommandHandler $deleteFromCartCommandHandler,
    )
    {
    }

    public function __invoke(
        #[MapRequestPayload] DeleteFromCartCommand $deleteFromCartCommand
    ): Response
    {
        $this->deleteFromCartCommandHandler->handle($deleteFromCartCommand);

        return $this->singleObjectResponse(null, Response::HTTP_OK);
    }
}
