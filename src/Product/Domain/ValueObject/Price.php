<?php

declare(strict_types=1);

namespace App\Product\Domain\ValueObject;

use Doctrine\ORM\Mapping\Embeddable;
use Doctrine\ORM\Mapping as ORM;

#[Embeddable]
final class Price
{
    #[ORM\Column(name: 'amount', type: 'integer')]
    private int $amount;

    #[ORM\Column(name: 'currency', type: 'string', length: 3)]
    private string $currency;

    private function __construct(int $amount, string $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }

    public static function fromFloat(float $amount, string $currency): self
    {
        return new self(
            intval($amount * 100),
            $currency,
        );
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getOriginalAmount(): float
    {
        return $this->amount / 100;
    }
}
