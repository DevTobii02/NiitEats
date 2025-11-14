<?php
require_once __DIR__ . '/../config/database.php';

class Payment {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function makePayment($orderId, $paymentMethod, $amount, $transactionId = null) {
        if ($transactionId === null) {
            $transactionId = uniqid('txn_');
        }
        $stmt = $this->conn->prepare("INSERT INTO payments (order_id, payment_method, amount, payment_status, transaction_id) VALUES (:order_id, :payment_method, :amount, 'successful', :transaction_id)");
        $stmt->bindParam(':order_id', $orderId);
        $stmt->bindParam(':payment_method', $paymentMethod);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':transaction_id', $transactionId);
        return $stmt->execute();
    }
}
?>
