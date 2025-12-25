<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Ac1dmarv3l\Eshop\Controller\CartController;
use GuzzleHttp\Client;

require __DIR__ . '/../vendor/autoload.php';

session_start();

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
$dotenv->required(['TG_BOT_TOKEN', 'TG_CHAT_ID']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$token = $_ENV['TG_BOT_TOKEN'] ?? '';
$chatId = $_ENV['TG_CHAT_ID'] ?? '';

$action = $_POST['action'] ?? '';

$controller = new CartController(new Client());

$result = match ($action) {
    'cart/add' => $controller->add(),
    'cart/update' => $controller->update(),
    'cart/delete' => $controller->delete(),
    'cart/get' => $controller->get(),
    'products/get' => $controller->getProducts(),
    'cart/checkout' => $controller->checkout($token, $chatId),
    default => ['success' => false, 'message' => 'Invalid action'],
};
echo json_encode($result);
