<?php

declare(strict_types=1);

namespace App\Cart\Application\UseCase\AddToCart;

use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

final class AddToCartCommand
{
    #[NotBlank]
    public string $productId;

    #[NotBlank]
    #[Type('integer')]
    #[GreaterThan(0, message: 'The value must be in range 1-9999')]
    #[LessThanOrEqual(9999, message: 'The value must be in range 1-9999')]
    public int $quantity;
}
