<?php
require_once __DIR__ . '/../utils/jwtUtil.php';

class AuthMiddleware {
    public static function handle() {
        $token = JwtUtil::getBearerToken();
        if (!$token) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'No token provided']);
            exit();
        }
        $decoded = JwtUtil::decode($token);
        if (!$decoded) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Invalid token']);
            exit();
        }
    }

    public static function getUserFromToken() {
        $token = JwtUtil::getBearerToken();
        if ($token) {
            return JwtUtil::decode($token);
        }
        return null;
    }
}
?>
