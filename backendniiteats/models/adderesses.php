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

// Handle GET: fetch all addresses for a user
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM addresses" . ($user_id ? " WHERE user_id='$user_id'" : "");
    $result = mysqli_query($conn, $sql);
    $addresses = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $addresses[] = $row;
    }
    echo json_encode($addresses);
    exit;
}

// Handle POST: add new address
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = $data['user_id'] ?? $user_id;
    $address_line = $data['address_line'] ?? '';
    $city = $data['city'] ?? '';
    $state = $data['state'] ?? '';
    $postal_code = $data['postal_code'] ?? '';
    $country = $data['country'] ?? '';

    $sql = "INSERT INTO addresses (user_id, address_line, city, state, postal_code, country)
            VALUES ('$user_id', '$address_line', '$city', '$state', '$postal_code', '$country')";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'id' => mysqli_insert_id($conn)]);
    } else {
        echo json_encode(['error' => mysqli_error($conn)]);
    }
    exit;
}

// Handle DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $id = $data['id'] ?? null;
    if ($id) {
        mysqli_query($conn, "DELETE FROM addresses WHERE id='$id'");
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'ID required']);
    }
    exit;
}