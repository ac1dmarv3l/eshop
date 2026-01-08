<?php

declare(strict_types=1);

namespace App\Product\Application\Controller\Api;

use App\Product\Application\UseCase\ListProducts\ListProductsQueryHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/products', methods: ['GET'])]
class ListProductsController extends AbstractController
{
    public function __construct(
        private readonly ListProductsQueryHandler $listProductsQueryHandler,
    )
    {
    }

    public function __invoke(): Response
    {
        $productCollectionDto = $this->listProductsQueryHandler->handle();

        return $this->json(['success' => true, 'products' => $productCollectionDto->products]);
    }
}
