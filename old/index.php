
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return json_encode(['success' => false, 'message' => 'Invalid request method']);
}

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
