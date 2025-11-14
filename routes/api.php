<?php
// Include configuration and database connection
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Include controllers
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../controllers/CategoriesController.php';

// Include middleware (optional)
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

header('Content-Type: application/json');

// Get the requested endpoint and method
$endpoint = $_GET['endpoint'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Route requests
switch ($endpoint) {
    case 'register':
    if ($method == 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        if ($data) {
            $auth = new AuthController($conn);
            echo json_encode($auth->register($data['username'], $data['email'], $data['password']));
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        }
    }
    break;


    case 'login':
        if ($method == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            if ($data) {
                $auth = new AuthController($conn);
                echo json_encode($auth->login($data['email'], $data['password']));
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
            }
        }
        break;

    case 'products':
        if ($method == 'GET') {
            $product = new ProductController($conn);
            $userId = $_GET['user_id'] ?? null;
            echo json_encode($product->getAllProducts($userId));
        }
        break;

    case 'products/details':
        if ($method == 'GET') {
            $productId = $_GET['id'] ?? null;
            $userId = $_GET['user_id'] ?? null;
            if ($productId) {
                $product = new ProductController($conn);
                echo json_encode($product->getProductById($productId, $userId));
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Product ID required']);
            }
        }
        break;

    case 'products/reviews':
        if ($method == 'GET') {
            $productId = $_GET['product_id'] ?? null;
            if (!$productId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Product ID required']);
                break;
            }
            $product = new ProductController($conn);
            echo json_encode($product->getReviewsByProduct($productId));
        } elseif ($method == 'POST') {
            AuthMiddleware::handle();
            $data = json_decode(file_get_contents('php://input'), true);
            $user = AuthMiddleware::getUserFromToken();
            $userId = $user['user_id'];
            $rating = $data['rating'] ?? null;
            $comment = $data['comment'] ?? null;
            if ($userId && $rating && $comment) {
                $product = new ProductController($conn);
                $result = $product->addReview($userId, $productId, $rating, $comment);
                echo json_encode(['success' => $result, 'message' => $result ? 'Review added' : 'Failed to add review']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Rating and comment required']);
            }
        }
        break;

    case 'products/like':
        AuthMiddleware::handle();
        if ($method == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $user = AuthMiddleware::getUserFromToken();
            $userId = $user['user_id'];
            $productId = $data['product_id'] ?? null;
            if ($userId && $productId) {
                $product = new ProductController($conn);
                $result = $product->toggleLike($userId, $productId);
                echo json_encode(['success' => $result, 'message' => $result ? 'Like toggled' : 'Failed to toggle like']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Product ID required']);
            }
        }
        break;

    case 'products/favorite':
        AuthMiddleware::handle();
        if ($method == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $user = AuthMiddleware::getUserFromToken();
            $userId = $user['user_id'];
            $productId = $data['product_id'] ?? null;
            if ($userId && $productId) {
                $product = new ProductController($conn);
                $result = $product->toggleFavorite($userId, $productId);
                echo json_encode(['success' => $result, 'message' => $result ? 'Favorite toggled' : 'Failed to toggle favorite']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Product ID required']);
            }
        }
        break;

    case 'favorites':
        AuthMiddleware::handle();
        if ($method == 'GET') {
            $user = AuthMiddleware::getUserFromToken();
            $userId = $user['user_id'];
            $product = new ProductController($conn);
            echo json_encode($product->getUserFavorites($userId));
        }
        break;

    case 'categories':
        if ($method == 'GET') {
            $categories = new CategoriesController($conn);
            echo json_encode($categories->getAllCategories());
        }
        break;

    case 'categories/products':
        if ($method == 'GET') {
            $categoryId = $_GET['category_id'] ?? null;
            if ($categoryId) {
                $categories = new CategoriesController($conn);
                echo json_encode($categories->getProductsByCategory($categoryId));
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Category ID required']);
            }
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Endpoint not found']);
        break;
}
?>
