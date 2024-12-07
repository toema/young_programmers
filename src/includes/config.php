<?php
session_start();

// تفعيل عرض الأخطاء للتطوير
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// معلومات قاعدة البيانات
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'youngdev');

// معلومات الموقع
define('SITE_NAME', 'مبرمج الصغير|YoungDev');
define('SITE_URL', 'http://localhost/youngdev/src');

// مسارات رفع الملفات
define('UPLOAD_PATH', __DIR__ . '/../uploads/profiles/');
define('UPLOAD_URL', SITE_URL . '/uploads/profiles/');

// ثوابت إضافية للمجتمع
define('MAX_POST_LENGTH', 1000);
define('MIN_POST_LENGTH', 10);
define('MAX_COMMENT_LENGTH', 500);
define('MIN_COMMENT_LENGTH', 2);

// الاتصال بقاعدة البيانات
try {
    $conn = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
    
    // التحقق من الاتصال
    $conn->query("SELECT 1");
    
} catch(PDOException $e) {
    // تسجيل الخطأ
    error_log("Database Connection Error: " . $e->getMessage());
    
    // عرض رسالة خطأ للمستخدم
    die("عذراً، حدث خطأ في الاتصال بقاعدة البيانات. الرجاء المحاولة لاحقاً.");
}

// التأكد من وجود الجداول المطلوبة
try {
    $required_tables = ['users', 'posts', 'comments', 'reactions'];
    $existing_tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    foreach($required_tables as $table) {
        if(!in_array($table, $existing_tables)) {
            error_log("Missing required table: " . $table);
        }
    }
} catch(PDOException $e) {
    error_log("Error checking tables: " . $e->getMessage());
}

// تضمين الدوال
require_once 'functions.php';
