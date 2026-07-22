<?php
session_start();
require_once 'config/db.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. استلام وتجهيز المدخلات
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    // 2. التحقق من الشروط (Validation)
    if (empty($username) || strlen($username) < 3) {
        $errors[] = "اسم المستخدم يجب أن يكون 3 حروف على الأقل.";
    }
    if (strlen($password) < 6) {
        $errors[] = "كلمة المرور يجب أن لا تقل عن 6 خانات.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "كلمتا المرور غير متطابقتين.";
    }

    // 3. إذا لم تكن هناك أخطاء مدخلات
    if (empty($errors)) {
        // فحص تكرار اسم المستخدم
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        
        if ($check_stmt) {
            $check_stmt->bind_param("s", $username);
            $check_stmt->execute();
            $result = $check_stmt->get_result();

            if ($result->num_rows > 0) {
                $errors[] = "اسم المستخدم موجود بالفعل! اختر اسماً آخر.";
            } else {
                // حفظ الحساب الجديد
                $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("sss", $username, $password, $role);
                    if ($stmt->execute()) {
                        $success = "تم إنشاء الحساب بنجاح! <a href='login.php' class='fw-bold text-success'>تسجيل الدخول الآن</a>";
                    } else {
                        $errors[] = "حدث خطأ أثناء حفظ الحساب.";
                    }
                } else {
                    $errors[] = "خطأ في استعلام حفظ البيانات.";
                }
            }
        } else {
            $errors[] = "خطأ في استعلام الفحص.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب جديد - نظام إدارة المخزون</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <style>
        body { background-color: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .register-card { background: #ffffff; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); border: none; }
        .btn-custom { background-color: #0d9488; color: #ffffff; font-weight: 600; }
        .btn-custom:hover { background-color: #0f766e; color: #ffffff; }
    </style>
</head>
<body class="d-flex align-items-center py-5 min-vh-100">

    <div class="container" style="max-width: 450px;">
        <div class="card register-card p-4">
            
            <h3 class="text-center fw-bold mb-4 text-dark">إنشاء حساب جديد</h3>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger rounded-3 mb-3">
                    <ul class="m-0 px-3 small">
                        <?php foreach ($errors as $err): ?>
                            <li><?php echo $err; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success rounded-3 mb-3 small text-center">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">اسم المستخدم</label>
                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required placeholder="مثال: admin">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">كلمة المرور</label>
                    <input type="password" name="password" class="form-control" required placeholder="******">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">تأكيد كلمة المرور</label>
                    <input type="password" name="confirm_password" class="form-control" required placeholder="******">
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary">نوع الحساب (الصلاحية)</label>
                    <select name="role" class="form-select">
                        <option value="user">مستخدم عادي (User)</option>
                        <option value="admin" selected>مسؤول النظام (Admin)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-custom w-100 py-2 rounded-3 shadow-sm mb-3">
                    تسجيل الحساب
                </button>
            </form>

            <div class="text-center">
                <a href="login.php" class="text-decoration-none small text-muted">
                    لديك حساب بالفعل؟ <span class="text-primary fw-bold">سجل دخولك هنا</span>
                </a>
            </div>

        </div>
    </div>

</body>
</html>