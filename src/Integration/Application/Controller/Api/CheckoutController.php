<?php

declare(strict_types=1);

namespace App\Integration\Application\Controller\Api;

use App\Common\Application\Controller\AbstractController;
use App\Integration\Application\UseCase\Checkout\CheckoutCommand;
use App\Integration\Application\UseCase\Checkout\CheckoutCommandHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/v1/checkout', methods: ['POST'])]
final class CheckoutController extends AbstractController
{
    public function __construct(
        private readonly CheckoutCommandHandler $checkoutCommandHandler,
    )
    {
    }

    public function __invoke(
        #[MapRequestPayload] CheckoutCommand $checkoutCommand,
    ): Response
    {
        $this->checkoutCommandHandler->handle($checkoutCommand);

        return $this->singleObjectResponse(null, Response::HTTP_OK);
    }
}
