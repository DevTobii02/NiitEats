<?php
require_once __DIR__ . '/../config/database.php';

class Cart {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addToCart($userId, $productId, $quantity) {
        $stmt = $this->conn->prepare("INSERT INTO carts (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':quantity', $quantity);
        return $stmt->execute();
    }

    public function getCartByUserId($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM carts WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateCartItem($userId, $productId, $quantity) {
        $stmt = $this->conn->prepare("UPDATE carts SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        return $stmt->execute();
    }

    public function removeFromCart($userId, $productId) {
        $stmt = $this->conn->prepare("DELETE FROM carts WHERE user_id = :user_id AND product_id = :product_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        return $stmt->execute();
    }
}
?>
