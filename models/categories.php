<?php
require_once __DIR__ . '/../config/database.php';

class Category {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllCategories() {
        $stmt = $this->conn->prepare("SELECT * FROM categories ORDER BY category_name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductsByCategory($categoryId) {
        $stmt = $this->conn->prepare("
            SELECT p.*,
                   COUNT(DISTINCT r.id) as review_count,
                   AVG(r.rating) as average_rating,
                   COUNT(DISTINCT l.id) as like_count
            FROM products p
            LEFT JOIN reviews r ON p.id = r.product_id
            LEFT JOIN likes l ON p.id = l.product_id
            WHERE p.category_id = :category_id AND p.availability_status = 'available'
            GROUP BY p.id
            ORDER BY p.created_at DESC
        ");
        $stmt->bindParam(':category_id', $categoryId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
