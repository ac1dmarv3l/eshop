<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$token = $_ENV['TG_BOT_TOKEN'] ?? '';
$chatId = $_ENV['TG_CHAT_ID'] ?? '';

if (empty($token) || empty($chatId)) {
    echo json_encode(['success' => false, 'message' => 'Telegram configuration missing']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$model = $_POST['model'] ?? '';
$quantity = $_POST['quantity'] ?? '';

if (empty($model) || empty($quantity) || !is_numeric($quantity) || $quantity < 1 || $quantity > 99) {
    echo json_encode(['success' => false, 'message' => 'Invalid form data']);
    exit;
}

$ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'];
$userAgent = $_SERVER['HTTP_USER_AGENT'];

$message = "New order received:\n" .
           "Model: {$model}\n" .
           "Quantity: {$quantity}\n" .
           "Ip: {$ip}\n" .
           "User-Agent: {$userAgent}";

$url = 'https://api.telegram.org/bot' . $token . '/sendMessage';

$params = [
    'chat_id' => $chatId,
    'text' => $message,
];

$client = new Client();

try {
    $response = $client->post($url, ['form_params' => $params]);
    $responseBody = $response->getBody()->getContents();
    $responseData = json_decode($responseBody, true);

    if ($responseData['ok']) {
        echo json_encode(['success' => true, 'message' => 'Order sent to Telegram']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send message to Telegram']);
    }
} catch (GuzzleException $e) {
    echo json_encode(['success' => false, 'message' => 'Error sending message']);
}
