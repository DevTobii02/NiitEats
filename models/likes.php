<?php
require_once __DIR__ . '/../config/database.php';

class Like {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addLike($userId, $productId) {
        $stmt = $this->conn->prepare("INSERT INTO likes (user_id, product_id) VALUES (:user_id, :product_id)");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        return $stmt->execute();
    }

    public function removeLike($userId, $productId) {
        $stmt = $this->conn->prepare("DELETE FROM likes WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        return $stmt->execute();
    }

    public function getLikesByProduct($productId) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as like_count FROM likes WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['like_count'];
    }

    public function checkIfLiked($userId, $productId) {
        $stmt = $this->conn->prepare("SELECT id FROM likes WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }
}
?>
