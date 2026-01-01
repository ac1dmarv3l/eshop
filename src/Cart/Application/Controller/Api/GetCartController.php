<?php

declare(strict_types=1);

namespace App\Cart\Application\Controller\Api;

use App\Cart\Application\UseCase\Get\GetCartQueryHandler;
use App\Common\Application\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/cart', methods: ['GET'])]
final class GetCartController extends AbstractController
{
    public function __construct(
        private readonly GetCartQueryHandler $getCartQueryHandler,
    )
    {
    }

    public function __invoke(): Response
    {
        $result = $this->getCartQueryHandler->handle();

        return $this->singleObjectResponse(['cart' => $result], Response::HTTP_OK);
    }
}
