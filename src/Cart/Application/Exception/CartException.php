<?php

declare(strict_types=1);

namespace App\Cart\Application\Exception;

final class CartException extends \Exception
{
    public static function fromString(string $message): self
    {
        return new self($message);
    }
}
