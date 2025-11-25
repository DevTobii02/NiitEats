<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

class AuthController {
    private $conn;

    public function __construct($dbConn) {
        $this->conn = $dbConn;
    }

    // ✅ User registration
    public function register($username, $email, $password) {
        // Check if user already exists
        $checkStmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $checkStmt->bindParam(':email', $email);
        $checkStmt->execute();

        if ($checkStmt->rowCount() > 0) {
            return ['success' => false, 'message' => 'Email already exists'];
        }

        // Hash the password securely
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'User registered successfully'];
        } else {
            return ['success' => false, 'message' => 'Registration failed'];
        }
    }

    // ✅ User login
    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Generate JWT token
            require_once __DIR__ . '/../utils/jwtUtil.php';
            $payload = [
                'user_id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email']
            ];
            $token = JwtUtil::encode($payload);

            return [
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email']
                ]
            ];
        } else {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }
    }
}
?>
