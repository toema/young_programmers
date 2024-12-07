<?php
define('DB_HOST', 'db');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'youngdev');

try {
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    echo "Connection successful!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
