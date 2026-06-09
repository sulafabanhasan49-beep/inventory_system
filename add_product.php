<?php
// بدء الجلسة للتحقق من صلاحيات الدخول
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// تضمين ملف الاتصال بقاعدة البيانات
include 'config/db.php';

// حماية الصفحة: منع غير المسجلين من الدخول
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// جلب الأقسام المتوفرة من قاعدة البيانات لعرضها في القائمة المنسدلة
$categories = $conn->query("SELECT * FROM categories");

// معالجة البيانات عند الضغط على زر "إدخال المنتج"
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category_id = intval($_POST['category_id']);
    $price = floatval($_POST['price']);
    $stock_quantity = intval($_POST['stock_quantity']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    $image_name = "";

    // رفع ومعالجة صورة المنتج
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['product_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $image_name = time() . '_' . $filename;
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }
            move_uploaded_file($_FILES['product_image']['tmp_name'], 'uploads/' . $image_name);
        }
    }

    // --- كود حماية ذكي: التأكد من وجود القسم في قاعدة البيانات قبل إدخال المنتج ---
    $check_cat = $conn->query("SELECT id FROM categories WHERE id = $category_id");
    if ($check_cat->num_rows == 0 && $category_id == 1) {
        // إذا كان الجدول فارغاً، ننشئ قسم "أثاث مكتبي" برقم 1 تلقائياً
        $conn->query("INSERT INTO categories (id, name) VALUES (1, 'أثاث مكتبي')");
    }
    // --------------------------------------------------------------------------

    // استعلام الإدخال المتوافق مع بنية جداولك الحالية
    $sql = "INSERT INTO products (name, category_id, price, stock_quantity, description, image_url) 
            VALUES ('$name', $category_id, $price, $stock_quantity, '$description', '$image_name')";
            
    if ($conn->query($sql)) {
        $message = "<div class='alert alert-success mt-3'>تم إضافة المنتج الجديد إلى المستودع بنجاح!</div>";
        // تحديث متغير الأقسام مجدداً ليعمل بعد الإضافة التلقائية
        $categories = $conn->query("SELECT * FROM categories");
    } else {
        $message = "<div class='alert alert-danger mt-3'>خطأ أثناء الإضافة: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة منتج جديد - نظام إدارة المخزون</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 card p-4 shadow-sm">
            <h2 class="mb-4 text-success">إضافة منتج جديد للمخزن</h2>
            
            <?php echo $message; ?>
            
            <form action="add_product.php" method="POST" enctype="multipart/form-data" class="mt-3">
                <div class="mb-3">
                    <label class="form-label">اسم المنتج</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">قسم المنتج (التصنيف)</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- اختر القسم المناسب --</option>
                        <?php 
                        if ($categories && $categories->num_rows > 0): 
                            while ($cat = $categories->fetch_assoc()): 
                                echo "<option value='" . $cat['id'] . "'>" . $cat['name'] . "</option>";
                            endwhile;
                        else:
                            echo "<option value='1'>قسم افتراضي (أثاث مكتبي)</option>";
                        endif; 
                        ?>
                    </select>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">السعر ($)</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">الكمية المتاحة في المستودع</label>
                        <input type="number" name="stock_quantity" class="form-control" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">وصف المنتج تفصيلياً</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">تحميل صورة للمنتج (اختياري)</label>
                    <input type="file" name="product_image" class="form-control">
                </div>
                
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success px-4">إدخال المنتج</button>
                    <a href="admin_dashboard.php" class="btn btn-secondary">إلغاء والعودة للوحة التحكم</a>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>