<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Ac1dmarv3l\Eshop\Controller\CartController;

require __DIR__ . '/../vendor/autoload.php';

session_start();

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$dotenv->required(['TG_BOT_TOKEN', 'TG_CHAT_ID']);

$token = $_ENV['TG_BOT_TOKEN'] ?? '';
$chatId = $_ENV['TG_CHAT_ID'] ?? '';

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'cart/add':
        $controller = new CartController();
        $controller->add();
        break;
    case 'cart/update':
        $controller = new CartController();
        $controller->update();
        break;
    case 'cart/delete':
        $controller = new CartController();
        $controller->delete();
        break;
    case 'cart/get':
        $controller = new CartController();
        $controller->get();
        break;
    case 'products/get':
        $controller = new CartController();
        $controller->getProducts();
        break;
    case 'cart/checkout':
        $controller = new CartController();
        $controller->checkout($token, $chatId);
        break;
    default:
        break;
}
