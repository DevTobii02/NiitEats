<?php
header('Content-Type: application/json');
include_once '../config/database.php';
session_start();

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// GET: fetch all categories
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = mysqli_query($conn, "SELECT * FROM categories ORDER BY id DESC");
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) $categories[] = $row;
    echo json_encode($categories);
    exit;
}

// POST: add category
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = $data['name'] ?? '';
    $description = $data['description'] ?? '';

    $check = mysqli_query($conn, "SELECT * FROM categories WHERE name='$name'");
    if (mysqli_num_rows($check) > 0) {
        echo json_encode(['error' => 'Category already exists']);
        exit;
    }

    $sql = "INSERT INTO categories (name, description) VALUES ('$name', '$description')";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'id' => mysqli_insert_id($conn)]);
    } else {
        echo json_encode(['error' => mysqli_error($conn)]);
    }
    exit;
}

// DELETE: remove category
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $id = $data['id'] ?? null;
    if ($id) {
        mysqli_query($conn, "DELETE FROM categories WHERE id='$id'");
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'ID required']);
    }
    exit;
}

// PUT (optional): update category
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $data);
    $id = $data['id'] ?? null;
    $name = $data['name'] ?? '';
    $description = $data['description'] ?? '';

    if ($id) {
        mysqli_query($conn, "UPDATE categories SET name='$name', description='$description' WHERE id='$id'");
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'ID required']);
    }
    exit;
}