<?php
// config/config.php

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'niiteats');

// Application configuration
define('APP_NAME', 'Niit Eats');
define('BASE_URL', 'http://localhost/niiteats/');
define('APP_ENV', 'development');

// Error reporting (optional)
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
}
?>