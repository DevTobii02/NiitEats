<?php
header('Content-Type: application/json');
include_once '../config/database.php';
session_start();

// Logged-in user
$user_id = $_SESSION['user_id'] ?? null;

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// --- GET: fetch reviews --- //
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $product_id = $_GET['product_id'] ?? null;

    if (!$product_id) {
        echo json_encode(['error' => 'product_id required']);
        exit;
    }

    $sql = "SELECT r.id, r.product_id, r.user_id, r.rating, r.comment, r.created_at, u.username
            FROM reviews r
            LEFT JOIN users u ON r.user_id = u.id
            WHERE r.product_id='$product_id'
            ORDER BY r.created_at DESC";

    $result = mysqli_query($conn, $sql);
    $reviews = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $reviews[] = $row;
    }

    echo json_encode($reviews);
    exit;
}

// --- POST: add review --- //
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $product_id = $data['product_id'] ?? null;
    $rating = $data['rating'] ?? null;
    $comment = $data['comment'] ?? '';
    $user_id = $user_id ?? ($data['user_id'] ?? null);

    if (!$product_id || !$rating || !$user_id) {
        echo json_encode(['error' => 'product_id, rating, and user_id required']);
        exit;
    }

    $sql = "INSERT INTO reviews (product_id, user_id, rating, comment, created_at)
            VALUES ('$product_id', '$user_id', '$rating', '$comment', NOW())";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'id' => mysqli_insert_id($conn)]);
    } else {
        echo json_encode(['error' => mysqli_error($conn)]);
    }
    exit;
}

// --- PUT: update review --- //
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $data);
    $id = $data['id'] ?? null;
    $rating = $data['rating'] ?? null;
    $comment = $data['comment'] ?? null;

    if (!$id) {
        echo json_encode(['error' => 'Review ID required']);
        exit;
    }

    $updates = [];
    if ($rating !== null) $updates[] = "rating='$rating'";
    if ($comment !== null) $updates[] = "comment='$comment'";

    if ($updates) {
        $sql = "UPDATE reviews SET " . implode(', ', $updates) . " WHERE id='$id'";
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

// --- DELETE: remove review --- //
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents("php://input"), $data);
    $id = $data['id'] ?? null;

    if (!$id) {
        echo json_encode(['error' => 'Review ID required']);
        exit;
    }

    mysqli_query($conn, "DELETE FROM reviews WHERE id='$id'");
    echo json_encode(['success' => true]);
    exit;
}