<?php

declare(strict_types=1);

namespace App\Product\Application\Dto;

use App\Product\Domain\Product;
use App\Product\Domain\ValueObject\Price;

final readonly class ProductDto
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public float $priceAmount,
        public string $priceCurrency,
        public ?string $imageUrl,
    ) {}

    public static function fromEntity(Product $product): self
    {
        return new self(
            id: $product->getId(),
            name: $product->getName(),
            description: $product->getDescription(),
            priceAmount: $product->getPrice()->getOriginalAmount(),
            priceCurrency: $product->getPrice()->getCurrency(),
            imageUrl: $product->getImageUrl(),
        );
    }
}
