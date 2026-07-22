<?php
session_start();
require_once 'config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "يرجى تعبئة جميع الحقول!";
    } else {
        // البحث عن المستخدم باستخدام اسم المستخدم
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($row = $res->fetch_assoc()) {
                // التحقق من كلمة المرور (سواء مشفرة أو عادية)
                if (password_verify($password, $row['password']) || $password === $row['password']) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['role'] = $row['role'];

                    if ($row['role'] === 'admin') {
                        header("Location: admin_dashboard.php");
                    } else {
                        header("Location: index.php");
                    }
                    exit();
                } else {
                    $error = "كلمة المرور غير صحيحة!";
                }
            } else {
                $error = "اسم المستخدم غير موجود!";
            }
        } else {
            $error = "حدث خطأ في استعلام البيانات.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام المخزون</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <style>
        body { background-color: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-login { border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); border: none; }
        .btn-custom { background-color: #0d6efd; color: #fff; font-weight: 600; }
    </style>
</head>
<body class="d-flex align-items-center vh-100">
    <div class="container" style="max-width: 400px;">
        <div class="card card-login p-4 bg-white">
            <h3 class="text-center fw-bold mb-4 text-dark">تسجيل الدخول</h3>
            
            <?php if ($error): ?>
                <div class="alert alert-danger rounded-3 py-2 small mb-3"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">اسم المستخدم</label>
                    <input type="text" name="username" class="form-control" required placeholder="مثال: sulafa أو admin">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">كلمة المرور</label>
                    <input type="password" name="password" class="form-control" required placeholder="******">
                </div>
                <button type="submit" class="btn btn-custom w-100 py-2 rounded-3 shadow-sm mb-3">دخول</button>
            </form>
            <div class="text-center">
                <a href="register.php" class="text-decoration-none small text-muted">
                    ليس لديك حساب؟ <span class="text-primary fw-bold">إنشاء حساب جديد</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>