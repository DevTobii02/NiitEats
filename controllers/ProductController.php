<?php
require_once __DIR__ . '/../models/products.php';
require_once __DIR__ . '/../models/reviews.php';
require_once __DIR__ . '/../models/likes.php';
require_once __DIR__ . '/../models/favorites.php';

class ProductController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllProducts($userId = null) {
        $productModel = new Product($this->conn);
        return $productModel->getAllProductsWithDetails($userId);
    }

    public function getProductById($id, $userId = null) {
        $productModel = new Product($this->conn);
        return $productModel->getProductWithDetails($id, $userId);
    }

    public function addReview($userId, $productId, $rating, $comment) {
        $reviewModel = new Review($this->conn);
        return $reviewModel->addReview($userId, $productId, $rating, $comment);
    }

    public function getReviewsByProduct($productId) {
        $reviewModel = new Review($this->conn);
        return $reviewModel->getReviewsByProductId($productId);
    }

    public function toggleLike($userId, $productId) {
        $likeModel = new Like($this->conn);
        if ($likeModel->checkIfLiked($userId, $productId)) {
            return $likeModel->removeLike($userId, $productId);
        } else {
            return $likeModel->addLike($userId, $productId);
        }
    }

    public function toggleFavorite($userId, $productId) {
        $favoriteModel = new Favorite($this->conn);
        if ($favoriteModel->checkIfFavorited($userId, $productId)) {
            return $favoriteModel->removeFavorite($userId, $productId);
        } else {
            return $favoriteModel->addFavorite($userId, $productId);
        }
    }

    public function getUserFavorites($userId) {
        $favoriteModel = new Favorite($this->conn);
        return $favoriteModel->getFavoritesByUser($userId);
    }
}
?>
