<?php
session_start();

// التحقق من تسجيل الدخول
function checkLoggedIn() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

// التحقق من صلاحية المدير (Admin)
function checkAdmin() {
    checkLoggedIn();
    if ($_SESSION['role'] !== 'admin') {
        die("عذراً، هذه الصفحة مخصصة لمدير النظام فقط!");
    }
}
?>