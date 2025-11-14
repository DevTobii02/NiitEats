<?php
require_once __DIR__ . '/../config/database.php';

class Product {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllProducts() {
        $stmt = $this->conn->prepare("SELECT * FROM products");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getProductWithDetails($id, $userId = null) {
        $query = "
            SELECT p.*,
                   COUNT(DISTINCT r.id) as review_count,
                   AVG(r.rating) as average_rating,
                   COUNT(DISTINCT l.id) as like_count,
                   COUNT(DISTINCT f.id) as favorite_count
        ";
        if ($userId) {
            $query .= ",
                   (SELECT COUNT(*) FROM likes WHERE user_id = :user_id AND product_id = p.id) as user_liked,
                   (SELECT COUNT(*) FROM favorites WHERE user_id = :user_id AND product_id = p.id) as user_favorited
            ";
        }
        $query .= "
            FROM products p
            LEFT JOIN reviews r ON p.id = r.product_id
            LEFT JOIN likes l ON p.id = l.product_id
            LEFT JOIN favorites f ON p.id = f.product_id
            WHERE p.id = :id
            GROUP BY p.id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($userId) {
            $stmt->bindParam(':user_id', $userId);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllProductsWithDetails($userId = null) {
        $query = "
            SELECT p.*,
                   COUNT(DISTINCT r.id) as review_count,
                   AVG(r.rating) as average_rating,
                   COUNT(DISTINCT l.id) as like_count,
                   COUNT(DISTINCT f.id) as favorite_count
        ";
        if ($userId) {
            $query .= ",
                   (SELECT COUNT(*) FROM likes WHERE user_id = :user_id AND product_id = p.id) as user_liked,
                   (SELECT COUNT(*) FROM favorites WHERE user_id = :user_id AND product_id = p.id) as user_favorited
            ";
        }
        $query .= "
            FROM products p
            LEFT JOIN reviews r ON p.id = r.product_id
            LEFT JOIN likes l ON p.id = l.product_id
            LEFT JOIN favorites f ON p.id = f.product_id
            WHERE p.availability_status = 'available'
            GROUP BY p.id
            ORDER BY p.created_at DESC
        ";

        $stmt = $this->conn->prepare($query);
        if ($userId) {
            $stmt->bindParam(':user_id', $userId);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
