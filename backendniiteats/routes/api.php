// reurns json response and accept request from frontend 
<?php
header('Content-Type: application/json');

// Include configuration and database connection
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

// Include controllers
require_once __DIR__ . '/app/controllers/AuthController.php';
require_once __DIR__ . '/app/controllers/ProductController.php';

// Include middleware (optional)
require_once __DIR__ . '/app/middleware/AuthMiddleware.php';

// Get the requested endpoint and method
$endpoint = $_GET['endpoint'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Route requests
switch ($endpoint) {
    case 'register':
        if ($method == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $auth = new AuthController($conn);
            echo json_encode($auth->register($data['name'], $data['email'], $data['password']));
        }
        break;

    case 'login':
        if ($method == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $auth = new AuthController($conn);
            echo json_encode($auth->login($data['email'], $data['password']));
        }
        break;

    case 'products':
        if ($method == 'GET') {
            $product = new ProductController($conn);
            echo json_encode($product->getAllProducts());
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Endpoint not found']);
        break;
}
?>
