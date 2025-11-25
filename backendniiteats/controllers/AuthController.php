// This is the flder where authentications are handled like login, register, logout etc. 
<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

class AuthController {
    private $conn;

    public function __construct($dbConn) {
        $this->conn = $dbConn;
    }

    // User registration
    public function register($name, $email, $password) {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'User registered successfully'];
        } else {
            return ['success' => false, 'message' => 'Registration failed'];
        }
    }

    // User login
    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Start session or generate token
            session_start();
            $_SESSION['user_id'] = $user['id'];
            return ['success' => true, 'message' => 'Login successful'];
        } else {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }
    }
}

// Usage example
$auth = new AuthController($conn);
// $auth->register('John Doe', 'john@example.com', 'secret123');
// $auth->login('john@example.com', 'secret123');
?>
