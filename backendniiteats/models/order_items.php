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

// GET: fetch items for a specific order
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $order_id = $_GET['order_id'] ?? null;

    if (!$order_id) {
        echo json_encode(['error' => 'order_id is required']);
        exit;
    }

    // Optional: check if order belongs to logged-in user
    if ($user_id) {
        $check_order = mysqli_query($conn, "SELECT * FROM orders WHERE id='$order_id' AND user_id='$user_id'");
        if (mysqli_num_rows($check_order) === 0) {
            echo json_encode(['error' => 'Order not found or access denied']);
            exit;
        }
    }

    $sql = "SELECT oi.id, oi.product_id, oi.quantity, oi.price, oi.subtotal, p.name
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id='$order_id'";
    $result = mysqli_query($conn, $sql);

    $items = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }

    echo json_encode($items);
    exit;
}

// POST: add item to an order (optional, mostly handled by checkout)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $order_id = $data['order_id'] ?? null;
    $product_id = $data['product_id'] ?? null;
    $quantity = $data['quantity'] ?? 1;
    $price = $data['price'] ?? 0;

    if (!$order_id || !$product_id) {
        echo json_encode(['error' => 'order_id and product_id required']);
        exit;
    }

    $subtotal = $price * $quantity;
    $sql = "INSERT INTO order_items (order_id, product_id, quantity, price, subtotal)
            VALUES ('$order_id', '$product_id', '$quantity', '$price', '$subtotal')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'id' => mysqli_insert_id($conn)]);
    } else {
        echo json_encode(['error' => mysqli_error($conn)]);
    }
    exit;
}

// DELETE: remove an item from an order (optional)
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $id = $data['id'] ?? null;
    if ($id) {
        mysqli_query($conn, "DELETE FROM order_items WHERE id='$id'");
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'ID required']);
    }
    exit;
}

// PUT: update quantity or price (optional)
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $data);
    $id = $data['id'] ?? null;
    $quantity = $data['quantity'] ?? null;
    $price = $data['price'] ?? null;

    if ($id && ($quantity !== null || $price !== null)) {
        $updates = [];
        if ($quantity !== null) $updates[] = "quantity='$quantity'";
        if ($price !== null) $updates[] = "price='$price', subtotal='$price'*quantity";
        $update_sql = "UPDATE order_items SET " . implode(', ', $updates) . " WHERE id='$id'";
        mysqli_query($conn, $update_sql);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Invalid parameters']);
    }
    exit;
}