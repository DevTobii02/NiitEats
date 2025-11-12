//codes that runs before accessing a route, before and after controllers
<?php
session_start();

class AuthMiddleware {
    public static function handle() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit();
        }
    }
}
?>
