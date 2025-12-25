<?php

declare(strict_types=1);

namespace Ac1dmarv3l\Eshop\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CartController
{
    private const string CART_SESSION_KEY = 'cart';
    private array $products;

    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->products = require __DIR__ . '/../config/products.php';
    }

    public function add(): void
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');

        $productId = $_POST['product_id'] ?? '';
        $quantity = (int) ($_POST['quantity'] ?? 1);

        if (empty($productId) || $quantity < 1 || $quantity > 999) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            exit;
        }

        $cart = $_SESSION[self::CART_SESSION_KEY] ?? [];
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }

        $_SESSION[self::CART_SESSION_KEY] = $cart;
        echo json_encode(['success' => true, 'cart' => $this->getCartItems()]);
    }

    public function update(): void
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');

        $productId = $_POST['product_id'] ?? '';
        $quantity = (int) ($_POST['quantity'] ?? 0);

        if (empty($productId) || $quantity < 0 || ($quantity > 0 && $quantity > 999)) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            exit;
        }

        $cart = $_SESSION[self::CART_SESSION_KEY] ?? [];
        if ($quantity === 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $quantity;
        }

        $_SESSION[self::CART_SESSION_KEY] = $cart;
        echo json_encode(['success' => true, 'cart' => $this->getCartItems()]);
    }

    public function delete(): void
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');

        $productId = $_POST['product_id'] ?? '';

        if (empty($productId)) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            exit;
        }

        $cart = $_SESSION[self::CART_SESSION_KEY] ?? [];
        unset($cart[$productId]);
        $_SESSION[self::CART_SESSION_KEY] = $cart;

        echo json_encode(['success' => true, 'cart' => $this->getCartItems()]);
    }

    public function get(): void
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');

        echo json_encode(['success' => true, 'cart' => $this->getCartItems()]);
    }

    public function getProducts(): void
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        $products = [];
        foreach ($this->products as $id => $product) {
            $products[] = array_merge(['id' => $id], $product);
        }

        echo json_encode(['success' => true, 'products' => $products]);
    }

    public function checkout(string $token, string $chatId): void
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');

        $cart = $_SESSION[self::CART_SESSION_KEY] ?? [];
        if (empty($cart)) {
            echo json_encode(['success' => false, 'message' => 'Cart is empty']);
            exit;
        }

        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';

        if (empty($email) || empty($phone)) {
            echo json_encode(['success' => false, 'message' => 'Email and phone are required']);
            exit;
        }

        $message = "New order received:\n";
        foreach ($cart as $productId => $quantity) {
            $productName = $this->products[$productId]['name'] ?? $productId;
            $message .= "$productName: $quantity\n";
        }
        $message .= "Email: $email\nPhone: $phone\n";

        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $message .= "Ip: $ip\nUser-Agent: $userAgent";

        $url = 'https://api.telegram.org/bot' . $token . '/sendMessage';
        $params = [
            'chat_id' => $chatId,
            'text' => $message,
        ];

        try {
            $response = $this->client->post($url, ['form_params' => $params]);
            $responseBody = $response->getBody()->getContents();
            $responseData = json_decode($responseBody, true);

            if ($responseData['ok']) {
                $_SESSION[self::CART_SESSION_KEY] = [];
                echo json_encode(['success' => true, 'message' => 'Order sent to Telegram']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to send message to Telegram']);
            }
        } catch (GuzzleException $e) {
            echo json_encode(['success' => false, 'message' => 'Error sending message: ' . $e->getMessage()]);
        }
    }

    private function getCartItems(): array
    {
        $cart = $_SESSION[self::CART_SESSION_KEY] ?? [];

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
