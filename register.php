<?php
include 'config/db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // تطهير المدخلات لحماية قاعدة البيانات من ثغرات الحقن (SQL Injection)
    $username = sanitize_input($_POST['username'], $conn);
    $password = $_POST['password'];

    // التحقق من أن اسم المستخدم غير مكرر في النظام
    $check_user = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($check_user);

    if ($result->num_rows > 0) {
        $message = "<div class='alert alert-danger'>خطأ: اسم المستخدم هذا مسجل بالفعل، اختر اسماً آخر.</div>";
    } else {
        // تشفير كلمة المرور أمنياً لحماية بيانات المستخدمين في قاعدة البيانات
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // إدخال المستخدم الجديد (الدور الافتراضي هو user)
        $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$hashed_password', 'user')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "<div class='alert alert-success'>تم إنشاء الحساب بنجاح! <a href='login.php'>اضغط هنا لتسجيل الدخول</a></div>";
        } else {
            $message = "<div class='alert alert-danger'>حدث خطأ أثناء التسجيل: " . $conn->error . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إنشاء حساب جديد</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 card p-4 shadow-sm">
            <h2 class="text-center mb-4">إنشاء حساب جديد</h2>
            
            <?php echo $message; // عرض رسائل التأكيد أو الخطأ هنا ?>
            
            <form action="register.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">اسم المستخدم</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">كلمة المرور</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success w-100">تسجيل الحساب</button>
                <p class="text-center mt-3">لديك حساب بالفعل؟ <a href="login.php">سجل دخولك هنا</a></p>
            </form>
        </div>
    </div>
</div>
</body>
</html>