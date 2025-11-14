<?php
require_once __DIR__ . '/../models/categories.php';

class CategoriesController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllCategories() {
        $categoryModel = new Category($this->conn);
        return $categoryModel->getAllCategories();
    }

    public function getProductsByCategory($categoryId) {
        $categoryModel = new Category($this->conn);
        return $categoryModel->getProductsByCategory($categoryId);
    }
}
?>
