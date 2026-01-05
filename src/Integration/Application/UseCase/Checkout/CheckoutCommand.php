<?php

declare(strict_types=1);

namespace App\Integration\Application\UseCase\Checkout;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Type;

final class CheckoutCommand
{
    #[NotBlank]
    #[Type('string')]
    #[Email]
    public string $email;

    #[NotBlank]
    #[Type('string')]
    public string $phone;

    #[NotBlank]
    #[Type('string')]
    #[Length(min: 3, max: 255)]
    public string $firstName;

    #[NotBlank]
    #[Type('string')]
    #[Length(min: 3, max: 255)]
    public string $lastName;

    #[NotBlank]
    #[Type('integer')]
    #[Length(exactly: 16)]
    public int $cardNumber;

    #[NotBlank]
    #[Type('integer')]
    #[Range(notInRangeMessage: 'Card expiry month is out of range', min: 1, max: 12)]
    public int $cardExpiryMonth;

    #[NotBlank]
    #[Type('integer')]
    #[Range(notInRangeMessage: 'Card expiry year is out of range', min: 2000, max: 2100)]
    public int $cardExpiryYear;

    #[NotBlank]
    #[Type('integer')]
    #[Range(min: 0, max: 999)]
    public int $cardCvv;

    #[NotBlank]
    #[Type('string')]
    #[Length(min: 5, max: 255)]
    public string $cardHolderName;
}
