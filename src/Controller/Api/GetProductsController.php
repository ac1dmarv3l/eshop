<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\ProductsProviderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/products', methods: ['GET'])]
class GetProductsController extends AbstractController
{
    private array $products;

    public function __construct(
        ProductsProviderService $productsProviderService,
    )
    {
        $this->products = $productsProviderService->getProducts();
    }

    public function __invoke(): Response
    {
        $products = [];
        foreach ($this->products as $id => $product) {
            $products[] = array_merge(['id' => $id], $product);
        }

        return $this->json(['success' => true, 'products' => $products]);
    }
}
