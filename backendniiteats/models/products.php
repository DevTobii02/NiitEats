<?php
header('Content-Type: application/json');
include_once '../config/database.php';
session_start();

// Optional: logged-in user/admin
$user_id = $_SESSION['user_id'] ?? null;

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// --- GET: fetch products --- //
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $category_id = $_GET['category_id'] ?? null;
    $sql = "SELECT p.id, p.name, p.description, p.price, p.category_id, c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id";

    if ($category_id) {
        $sql .= " WHERE p.category_id='$category_id'";
    }

    $sql .= " ORDER BY p.id DESC";
    $result = mysqli_query($conn, $sql);

    $products = [];
    while ($row = mysqli_fetch_assoc($result)) $products[] = $row;

    echo json_encode($products);
    exit;
}

// --- POST: add new product --- //
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = $data['name'] ?? '';
    $description = $data['description'] ?? '';
    $price = $data['price'] ?? 0;
    $category_id = $data['category_id'] ?? null;

    if (!$name || !$category_id) {
        echo json_encode(['error' => 'Product name and category_id required']);
        exit;
    }

    $sql = "INSERT INTO products (name, description, price, category_id)
            VALUES ('$name', '$description', '$price', '$category_id')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'id' => mysqli_insert_id($conn)]);
    } else {
        echo json_encode(['error' => mysqli_error($conn)]);
    }
    exit;
}

// --- PUT: update product --- //
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $data);
    $id = $data['id'] ?? null;
    $name = $data['name'] ?? null;
    $description = $data['description'] ?? null;
    $price = $data['price'] ?? null;
    $category_id = $data['category_id'] ?? null;

    if (!$id) {
        echo json_encode(['error' => 'Product ID required']);
        exit;
    }

    $updates = [];
    if ($name !== null) $updates[] = "name='$name'";
    if ($description !== null) $updates[] = "description='$description'";
    if ($price !== null) $updates[] = "price='$price'";
    if ($category_id !== null) $updates[] = "category_id='$category_id'";

    if ($updates) {
        $sql = "UPDATE products SET " . implode(', ', $updates) . " WHERE id='$id'";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['error' => 'No fields to update']);
    }
    exit;
}

// --- DELETE: remove product --- //
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $id = $data['id'] ?? null;

    if (!$id) {
        echo json_encode(['error' => 'Product ID required']);
        exit;
    }

    mysqli_query($conn, "DELETE FROM products WHERE id='$id'");
    echo json_encode(['success' => true]);
    exit;
}