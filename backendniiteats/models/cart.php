<?php
header('Content-Type: application/json');
include_once '../config/database.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// GET: fetch cart
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT c.id AS cart_id, c.product_id, c.quantity, p.name, p.price, (c.quantity*p.price) AS total
            FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id='$user_id'";
    $result = mysqli_query($conn, $sql);
    $cart = [];
    while ($row = mysqli_fetch_assoc($result)) $cart[] = $row;
    echo json_encode($cart);
    exit;
}

// POST: add/update cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $product_id = $data['product_id'] ?? 0;
    $quantity = $data['quantity'] ?? 1;

    $check = mysqli_query($conn, "SELECT * FROM cart WHERE user_id='$user_id' AND product_id='$product_id'");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE cart SET quantity=quantity+$quantity WHERE user_id='$user_id' AND product_id='$product_id'");
    } else {
        mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES ('$user_id', '$product_id', '$quantity')");
    }
    echo json_encode(['success' => true]);
    exit;
}

// DELETE: remove from cart
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $cart_id = $data['cart_id'] ?? null;
    if ($cart_id) {
        mysqli_query($conn, "DELETE FROM cart WHERE id='$cart_id'");
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Cart ID required']);
    }
    exit;
}