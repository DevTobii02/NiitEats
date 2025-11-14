<?php
require_once __DIR__ . '/../config/database.php';

class Favorite {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addFavorite($userId, $productId) {
        $stmt = $this->conn->prepare("INSERT INTO favorites (user_id, product_id) VALUES (:user_id, :product_id)");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        return $stmt->execute();
    }

    public function removeFavorite($userId, $productId) {
        $stmt = $this->conn->prepare("DELETE FROM favorites WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        return $stmt->execute();
    }

    public function getFavoritesByUser($userId) {
        $stmt = $this->conn->prepare("
            SELECT p.*, f.created_at as favorited_at
            FROM favorites f
            JOIN products p ON f.product_id = p.id
            WHERE f.user_id = :user_id
            ORDER BY f.created_at DESC
        ");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkIfFavorited($userId, $productId) {
        $stmt = $this->conn->prepare("SELECT id FROM favorites WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }
}
?>
