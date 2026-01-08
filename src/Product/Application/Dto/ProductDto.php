<?php

declare(strict_types=1);

namespace App\Product\Application\Dto;

use App\Product\Domain\Product;

final readonly class ProductDto
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public string $price,
        public ?string $imageUrl,
    ) {}

    public static function fromEntity(Product $product): self
    {
        return new self(
            id: $product->getId(),
            name: $product->getName(),
            description: $product->getDescription(),
            price: $product->getPrice(),
            imageUrl: $product->getImageUrl(),
        );
    }
}
