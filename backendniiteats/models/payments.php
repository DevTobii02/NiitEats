<?php
header('Content-Type: application/json');
include_once '../config/database.php';
session_start();

// Example: logged-in user
$user_id = $_SESSION['user_id'] ?? null;

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// --- GET: fetch payments --- //
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT p.id, p.order_id, p.user_id, p.amount, p.payment_method, p.status, p.created_at, o.total_amount
            FROM payments p
            LEFT JOIN orders o ON p.order_id = o.id";

    if ($user_id) {
        $sql .= " WHERE p.user_id='$user_id'";
    }

    $sql .= " ORDER BY p.created_at DESC";

    $result = mysqli_query($conn, $sql);
    $payments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $payments[] = $row;
    }

    echo json_encode($payments);
    exit;
}

// --- POST: create a payment --- //
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $order_id = $data['order_id'] ?? null;
    $amount = $data['amount'] ?? null;
    $payment_method = $data['payment_method'] ?? 'card';
    $status = $data['status'] ?? 'pending';
    $user_id = $user_id ?? ($data['user_id'] ?? null);

    if (!$order_id || !$amount || !$user_id) {
        echo json_encode(['error' => 'order_id, amount, and user_id are required']);
        exit;
    }

    $sql = "INSERT INTO payments (order_id, user_id, amount, payment_method, status, created_at)
            VALUES ('$order_id', '$user_id', '$amount', '$payment_method', '$status', NOW())";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'id' => mysqli_insert_id($conn)]);
    } else {
        echo json_encode(['error' => mysqli_error($conn)]);
    }
    exit;
}

// --- PUT: update payment status --- //
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $data);
    $id = $data['id'] ?? null;
    $status = $data['status'] ?? null;

    if (!$id || !$status) {
        echo json_encode(['error' => 'Payment ID and status required']);
        exit;
    }

    $sql = "UPDATE payments SET status='$status' WHERE id='$id'";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => mysqli_error($conn)]);
    }
    exit;
}

// --- DELETE: remove a payment (optional) --- //
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $id = $data['id'] ?? null;

    if (!$id) {
        echo json_encode(['error' => 'Payment ID required']);
        exit;
    }

    mysqli_query($conn, "DELETE FROM payments WHERE id='$id'");
    echo json_encode(['success' => true]);
    exit;
}