<?php

declare(strict_types=1);

namespace App\Cart\Application\Controller\Api;

use App\Cart\Application\UseCase\AddToCart\AddToCartCommand;
use App\Cart\Application\UseCase\AddToCart\AddToCartCommandHandler;
use App\Common\Application\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/v1/cart', methods: ['POST'])]
class AddToCartController extends AbstractController
{
    public function __construct(
        private readonly AddToCartCommandHandler $addToCartCommandHandler,
    )
    {

    }

    public function __invoke(
        #[MapRequestPayload] AddToCartCommand $addToCartCommand,
    ): Response
    {
        $this->addToCartCommandHandler->handle($addToCartCommand);

        return $this->singleObjectResponse(null, Response::HTTP_CREATED);
    }
}
