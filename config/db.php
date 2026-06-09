<?php
// تفعيل الجلسات (Sessions) لتعقب تسجيل الدخول وصلاحيات المستخدمين
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// معلومات الاتصال
$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'inventory_db';

// إنشاء الاتصال
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// التحقق من نجاح الاتصال وتنسيق رسالة الخطأ
if ($conn->connect_error) {
    die("<div style='color: red; padding: 15px; background: #f8d7da;'>فشل الاتصال بقاعدة البيانات: " . $conn->connect_error . "</div>");
}

$conn->set_charset("utf8mb4");

// دالة أمنية هامة للتحقق من صحة المدخلات وتطهيرها قبل استخدامها (Input Validation & Sanitization)
function sanitize_input($data, $conn) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data); // الحماية من ثغرات XSS
    return $conn->real_escape_string($data); // الحماية من SQL Injection
}
?>