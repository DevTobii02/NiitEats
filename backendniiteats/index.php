// handles loading of pages alo inludes controllers or views as needed
<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/ProductController.php';

// Simple routing example
$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'home':
        $productController = new ProductController($conn);
        $products = $productController->getAllProducts();
        include __DIR__ . '/../app/views/home.php';
        break;

    case 'cart':
        include __DIR__ . '/../app/views/cart.php';
        break;

    default:
        echo "Page not found";
        break;
}
?>
