<?php

declare(strict_types=1);

namespace App\Product\Application\Dto;

final readonly class ProductCollectionDto
{
    /** @param ProductDto[] $products */
    public function __construct(
        public array $products,
    ) {}
}
