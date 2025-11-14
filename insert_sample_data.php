<?php
require_once 'config/database.php';

try {
    // Insert sample categories
    $conn->exec("INSERT INTO categories (category_name, description) VALUES
        ('Appetizers', 'Light starters and snacks'),
        ('Main Courses', 'Hearty main dishes'),
        ('Desserts', 'Sweet treats and desserts'),
        ('Beverages', 'Drinks and refreshments')
    ");
    echo "Sample categories inserted\n";

    // Insert sample products
    $conn->exec("INSERT INTO products (name, description, price, category_id, availability_status, image_url) VALUES
        ('Caesar Salad', 'Fresh romaine lettuce with Caesar dressing', 8.99, 1, 'available', 'salad.jpg'),
        ('Grilled Chicken', 'Juicy grilled chicken breast with vegetables', 15.99, 2, 'available', 'chicken.jpg'),
        ('Chocolate Cake', 'Rich chocolate cake with frosting', 6.99, 3, 'available', 'cake.jpg'),
        ('Coffee', 'Freshly brewed coffee', 2.99, 4, 'available', 'coffee.jpg')
    ");
    echo "Sample products inserted\n";

} catch(PDOException $e) {
    echo "Error inserting sample data: " . $e->getMessage() . "\n";
}
?>
