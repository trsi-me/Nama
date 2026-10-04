<?php
/**
 * ملف الاتصال بقاعدة البيانات باستخدام PDO
 * موقع نماء - منصة العلوم الشرعية
 */

// إعدادات قاعدة البيانات
define('DB_HOST', 'localhost');
define('DB_USER', 'root');           // اسم المستخدم (root في XAMPP)
define('DB_PASS', '');               // كلمة المرور (فارغة في XAMPP)
define('DB_NAME', 'nemaa_db');       // اسم قاعدة البيانات

try {
    // إنشاء الاتصال باستخدام PDO
    $conn = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    // في حالة الفشل
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'فشل الاتصال بقاعدة البيانات: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE));
}
?>
