<?php
require_once __DIR__ . '/../config/database.php';

class Review {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addReview($userId, $productId, $rating, $comment) {
        $stmt = $this->conn->prepare("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (:user_id, :product_id, :rating, :comment)");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':comment', $comment);
        return $stmt->execute();
    }

    public function getReviewsByProductId($productId) {
        $stmt = $this->conn->prepare("SELECT r.*, u.username AS name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = :product_id ORDER BY r.created_at DESC");
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllReviews() {
        $stmt = $this->conn->prepare("SELECT r.*, u.username AS name, p.name AS product_name FROM reviews r JOIN users u ON r.user_id = u.id JOIN products p ON r.product_id = p.id ORDER BY r.created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
