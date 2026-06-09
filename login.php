<?php
session_start();
include 'config/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // التحقق من كلمة المرور المشفرة أو العادية (123456)
        if (password_verify($password, $user['password']) || $password == '123456') {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "كلمة المرور غير صحيحة!";
        }
    } else {
        $error = "اسم المستخدم غير موجود!";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول - نظام المخزون</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .login-container { max-width: 400px; margin-top: 100px; }
    </style>
</head>
<body>
<div class="container login-container">
    <div class="card p-4 shadow-sm border-0">
        <h3 class="text-center text-primary mb-4">نظام إدارة المخزون الذكي</h3>
        <p class="text-muted text-center small">كلية الإدارة والتمويل</p>
        
        <?php if($error): ?>
            <div class="alert alert-danger text-center small"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="mb-3">
                <label class="form-label">اسم المستخدم</label>
                <input type="text" name="username" class="form-control" placeholder="admin" required>
            </div>
            <div class="mb-3">
                <label class="form-label">كلمة المرور</label>
                <input type="password" name="password" class="form-control" placeholder="123456" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-2">تسجيل الدخول</button>
        </form>
    </div>
</div>
</body>
</html>