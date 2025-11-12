<?php
header('Content-Type: application/json');
include_once '../config/database.php';
session_start();

// Example: logged-in user
$user_id = $_SESSION['user_id'] ?? null;

// Check database connection
if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// --- GET: fetch orders --- //
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Optional: get orders for a specific user
    $sql = "SELECT o.id, o.user_id, o.total_amount, o.status, o.created_at, u.username
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id";

    if ($user_id) {
        $sql .= " WHERE o.user_id='$user_id'";
    }

    $sql .= " ORDER BY o.created_at DESC";

    $result = mysqli_query($conn, $sql);
    $orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }

    echo json_encode($orders);
    exit;
}

// --- POST: create new order (optional, normally done via checkout.php) --- //
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = $data['user_id'] ?? $user_id;
    $total_amount = $data['total_amount'] ?? 0;
    $status = $data['status'] ?? 'pending';

    if (!$user_id || !$total_amount) {
        echo json_encode(['error' => 'User ID and total amount required']);
        exit;
    }

    $sql = "INSERT INTO orders (user_id, total_amount, status, created_at)
            VALUES ('$user_id', '$total_amount', '$status', NOW())";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'id' => mysqli_insert_id($conn)]);
    } else {
        echo json_encode(['error' => mysqli_error($conn)]);
    }
    exit;
}

// --- PUT: update order status --- //
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $data);
    $id = $data['id'] ?? null;
    $status = $data['status'] ?? null;

    if (!$id || !$status) {
        echo json_encode(['error' => 'Order ID and status required']);
        exit;
    }

    $sql = "UPDATE orders SET status='$status' WHERE id='$id'";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => mysqli_error($conn)]);
    }