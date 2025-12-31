<?php

declare(strict_types=1);

namespace App\Product\Application\Dto;

use Symfony\Component\Validator\Constraints\NotBlank;

final class ProductDto
{
    #[NotBlank]
    public string $productId;

    #[NotBlank]
    public string $quantity;
}
