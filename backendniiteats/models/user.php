<?php
header('Content-Type: application/json');
include_once '../config/database.php';
session_start();

// Check database connection
if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// --- GET: fetch users --- //
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = $_GET['id'] ?? null;
    $sql = "SELECT id, username, email, created_at FROM users";

    if ($user_id) {
        $sql .= " WHERE id='$user_id'";
    }

    $result = mysqli_query($conn, $sql);
    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }

    echo json_encode($users);
    exit;
}

// --- POST: add new user (registration) --- //
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    if (!$username || !$email || !$password) {
        echo json_encode(['error' => 'Username, email, and password required']);
        exit;
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Check for existing email
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        echo json_encode(['error' => 'Email already exists']);
        exit;
    }

    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password_hash')";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'id' => mysqli_insert_id($conn)]);
    } else {
        echo json_encode(['error' => mysqli_error($conn)]);
    }
    exit;
}

// --- PUT: update user --- //
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $data);
    $id = $data['id'] ?? null;
    $username = $data['username'] ?? null;
    $email = $data['email'] ?? null;
    $password = $data['password'] ?? null;

    if (!$id) {
        echo json_encode(['error' => 'User ID required']);
        exit;
    }

    $updates = [];
    if ($username !== null) $updates[] = "username='$username'";
    if ($email !== null) $updates[] = "email='$email'";
    if ($password !== null) $updates[] = "password='" . password_hash($password, PASSWORD_DEFAULT) . "'";

    if ($updates) {
        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id='$id'";
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

// --- DELETE: remove user --- //
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $id = $data['id'] ?? null;

    if (!$id) {
        echo json_encode(['error' => 'User ID required']);
        exit;
    }

    mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
    echo json_encode(['success' => true]);
    exit;
}