<?php

declare(strict_types=1);

namespace App\Product\Application\UseCase\ListProducts;

use App\Product\Application\Dto\ProductCollectionDto;
use App\Product\Application\Dto\ProductDto;
use App\Product\Domain\Product;
use App\Product\Infrastructure\Repository\ProductRepository;

final readonly class ListProductsQueryHandler
{
    public function __construct(
        private ProductRepository $productRepository,
    )
    {

    }

    public function handle(): ProductCollectionDto
    {
        $products = $this->productRepository->findAll();

        $productDtos = array_map(
            fn(Product $product) => ProductDto::fromEntity($product),
            $products
        );

        return new ProductCollectionDto($productDtos);
    }
}
