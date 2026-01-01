<?php

declare(strict_types=1);

namespace App\Cart\Application\UseCase\Delete;

use Symfony\Component\Validator\Constraints\NotBlank;

final class DeleteFromCartCommand
{
    #[NotBlank]
    public string $productId;
}
