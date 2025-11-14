<?php
require_once __DIR__ . '/../config/database.php';

class Address {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addAddress($userId, $street, $city, $state, $postalCode, $country, $isDefault = false) {
        $stmt = $this->conn->prepare("INSERT INTO addresses (user_id, street, city, state, postal_code, country, is_default) VALUES (:user_id, :street, :city, :state, :postal_code, :country, :is_default)");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':street', $street);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':state', $state);
        $stmt->bindParam(':postal_code', $postalCode);
        $stmt->bindParam(':country', $country);
        $stmt->bindParam(':is_default', $isDefault, PDO::PARAM_BOOL);
        return $stmt->execute();
    }

    public function getAddressesByUserId($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM addresses WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateAddress($id, $street, $city, $state, $postalCode, $country, $isDefault = false) {
        $stmt = $this->conn->prepare("UPDATE addresses SET street = :street, city = :city, state = :state, postal_code = :postal_code, country = :country, is_default = :is_default WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':street', $street);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':state', $state);
        $stmt->bindParam(':postal_code', $postalCode);
        $stmt->bindParam(':country', $country);
        $stmt->bindParam(':is_default', $isDefault, PDO::PARAM_BOOL);
        return $stmt->execute();
    }

    public function deleteAddress($id) {
        $stmt = $this->conn->prepare("DELETE FROM addresses WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getAllAddresses() {
        $stmt = $this->conn->prepare("SELECT a.*, u.name AS user_name FROM addresses a JOIN users u ON a.user_id = u.id");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
