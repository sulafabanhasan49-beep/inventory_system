<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// جلب بيانات المنتج الحالي لتعبئتها في المدخلات تلقائياً
$product_res = $conn->query("SELECT * FROM products WHERE id = $id");
if($product_res->num_rows == 0) {
    header("Location: admin_dashboard.php");
    exit();
}
$product = $product_res->fetch_assoc();
$categories = $conn->query("SELECT * FROM categories");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category_id = intval($_POST['category_id']);
    $price = floatval($_POST['price']);
    $stock_quantity = intval($_POST['stock_quantity']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    $image_name = $product['image_url']; // الاحتفاظ بالصورة القديمة افتراضياً

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['product_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $image_name = time() . '_' . $filename;
            move_uploaded_file($_FILES['product_image']['tmp_name'], 'uploads/' . $image_name);
        }
    }

    $sql = "UPDATE products SET name='$name', category_id=$category_id, price=$price, 
            stock_quantity=$stock_quantity, description='$description', image_url='$image_name' WHERE id=$id";
            
    if ($conn->query($sql)) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $message = "<div class='alert alert-danger'>خطأ أثناء التحديث: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعديل منتج - نظام إدارة المخزون</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 card p-4 shadow-sm">
            <h2 class="mb-4 text-warning">تعديل بيانات المنتج</h2>
            <?php echo $message; ?>
            <form action="edit_product.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">اسم المنتج</label>
                    <input type="text" name="name" class="form-control" value="<?php echo $product['name']; ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">قسم المنتج (التصنيف)</label>
                    <select name="category_id" class="form-select" required>
                        <?php while ($cat = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                                <?php echo $cat['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">السعر ($)</label>
                        <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $product['price']; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">الكمية المتاحة في المستودع</label>
                        <input type="number" name="stock_quantity" class="form-control" value="<?php echo $product['stock_quantity']; ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">وصف المنتج تفصيلياً</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo $product['description']; ?></textarea>
                </div>
                <div class="mb-4">
                    <label class="form-label">تغيير صورة المنتج (اختياري)</label>
                    <input type="file" name="product_image" class="form-control">
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-warning px-4">تحديث البيانات</button>
                    <a href="admin_dashboard.php" class="btn btn-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>