<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

// جلب المنتجات مع أسماء الأقسام الخاصة بها
$sql = "SELECT products.*, categories.name as category_name 
        FROM products 
        LEFT JOIN categories ON products.category_id = categories.id 
        ORDER BY products.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم - إدارة المخزون</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-custom { background-color: #1e3d59; }
        .table-container { background: #ffffff; border-radius: 8px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">نظام المخزون الذكي 📊</a>
        <div class="navbar-nav ms-auto">
            <span class="nav-link text-white active">مرحباً، <?php echo $_SESSION['username']; ?> (لوحة الإدارة)</span>
        </div>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-secondary fw-bold small-title">قائمة المنتجات الحالية في المستودع</h2>
        <a href="add_product.php" class="btn btn-success px-4 py-2 fw-bold">+ إضافة منتج جديد</a>
    </div>

    <div class="table-container border-0">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th>الصورة</th>
                    <th>اسم المنتج</th>
                    <th>التصنيف</th>
                    <th>السعر</th>
                    <th>الكمية بالمخزن</th>
                    <th>الوصف</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if($row['image_url']): ?>
                                    <img src="uploads/<?php echo $row['image_url']; ?>" width="50" height="50" class="rounded border shadow-sm">
                                <?php else: ?>
                                    <span class="text-muted small">لا توجد صورة</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo $row['name']; ?></strong></td>
                            <td><span class="badge bg-info text-dark px-2.5 py-1.5"><?php echo $row['category_name'] ? $row['category_name'] : 'قسم افتراضي'; ?></span></td>
                            <td class="text-success fw-bold">$<?php echo number_format($row['price'], 2); ?></td>
                            <td>
                                <span class="fw-bold <?php echo $row['stock_quantity'] < 5 ? 'text-danger' : 'text-secondary'; ?>">
                                    <?php echo $row['stock_quantity']; ?> وحدة
                                </span>
                            </td>
                            <td class="text-muted small" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?php echo $row['description']; ?>
                            </td>
                            <td>
                                <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning fw-bold px-3">تعديل</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">المستودع فارغ حالياً، ابدأ بإضافة منتجاتك المتميزة!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>