<?php
header('Content-Type: application/json');
include_once '../config/database.php';
session_start();

// Optional: check if current user is admin
$current_user_role = $_SESSION['role'] ?? 'manager';

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// GET: fetch all admins
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = mysqli_query($conn, "SELECT id, username, email, role, created_at FROM admin ORDER BY id DESC");
    $admins = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $admins[] = $row;
    }
    echo json_encode($admins);
    exit;
}

// POST: add admin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? '';
    $email = $data['email'] ?? '';
    $password = password_hash($data['password'] ?? '', PASSWORD_DEFAULT);
    $role = $data['role'] ?? 'manager';

    $sql = "INSERT INTO admin (username, email, password, role)
            VALUES ('$username', '$email', '$password', '$role')";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'id' => mysqli_insert_id($conn)]);
    } else {
        echo json_encode(['error' => mysqli_error($conn)]);
    }
    exit;
}

// DELETE: remove admin
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $id = $data['id'] ?? null;
    if ($id) {
        mysqli_query($conn, "DELETE FROM admin WHERE id='$id'");
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'ID required']);
    }
    exit;
}