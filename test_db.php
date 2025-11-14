<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=food_ordering_db;charset=utf8', 'root', '');
    echo 'Database connection successful';

    // Test if tables exist
    $tables = ['users', 'products', 'categories', 'reviews', 'likes', 'favorites'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "\nTable '$table' exists";
        } else {
            echo "\nTable '$table' does not exist";
        }
    }
} catch(PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>
