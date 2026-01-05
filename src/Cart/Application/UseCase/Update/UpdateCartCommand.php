<?php

declare(strict_types=1);

namespace App\Cart\Application\UseCase\Update;

use Symfony\Component\Validator\Constraints\NotBlank;

final class UpdateCartCommand
{
    #[NotBlank]
    public string $productId;

    #[NotBlank]
    public ?string $quantity = null;
}
