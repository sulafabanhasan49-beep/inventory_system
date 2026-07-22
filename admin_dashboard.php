<?php
session_start();
require_once 'config/db.php';

// رسائل التنبيه
$message = '';
$error = '';

// 1. إضافة قسم جديد
if (isset($_POST['add_category'])) {
    $cat_name = trim($_POST['category_name']);
    if (!empty($cat_name)) {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $cat_name);
        if ($stmt->execute()) {
            $message = "تمت إضافة القسم بنجاح!";
        } else {
            $error = "حدث خطأ أثناء إضافة القسم: " . $conn->error;
        }
    }
}

// 2. إضافة منتج جديد
if (isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $category_id = intval($_POST['category_id']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $description = trim($_POST['description']);
    $image = trim($_POST['image']);

    if (!empty($name) && $price >= 0) {
        $stmt = $conn->prepare("INSERT INTO products (name, category_id, price, stock_quantity, description, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sidiss", $name, $category_id, $price, $stock, $description, $image);
        if ($stmt->execute()) {
            $message = "تمت إضافة المنتج بنجاح!";
        } else {
            $error = "حدث خطأ أثناء إضافة المنتج: " . $conn->error;
        }
    } else {
        $error = "يرجى كتابة اسم المنتج والسعر بشكل صحيح.";
    }
}

// 3. تعديل منتج
if (isset($_POST['update_product'])) {
    $id = intval($_POST['product_id']);
    $name = trim($_POST['name']);
    $category_id = intval($_POST['category_id']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $description = trim($_POST['description']);
    $image = trim($_POST['image']);

    if ($id > 0 && !empty($name)) {
        $stmt = $conn->prepare("UPDATE products SET name=?, category_id=?, price=?, stock_quantity=?, description=?, image=? WHERE id=?");
        $stmt->bind_param("sidissi", $name, $category_id, $price, $stock, $description, $image, $id);
        if ($stmt->execute()) {
            $message = "تم تحديث المنتج بنجاح!";
        } else {
            $error = "حدث خطأ أثناء التحديث: " . $conn->error;
        }
    }
}

// 4. حذف منتج
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    if ($delete_id > 0) {
        $conn->query("DELETE FROM products WHERE id = $delete_id");
        header("Location: admin_dashboard.php?msg=deleted");
        exit();
    }
}

// جلب المنتجات
$products_result = $conn->query("SELECT products.*, categories.name AS category_name FROM products LEFT JOIN categories ON products.category_id = categories.id ORDER BY products.id DESC");

// جلب الأقسام
$categories_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$categories = [];
if ($categories_result && $categories_result->num_rows > 0) {
    while ($cat = $categories_result->fetch_assoc()) {
        $categories[] = $cat;
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - إدارة المخزون</title>
    <!-- Bootstrap 5 RTL & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-custom { background-color: #0f172a; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .table-img { width: 48px; height: 48px; object-fit: cover; border-radius: 8px; }
    </style>
</head>
<body>

    <!-- الهيدر العلوي -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3 shadow-sm">
        <div class="container px-4">
            <a class="navbar-brand fw-bold fs-4 d-flex align-items-center" href="admin_dashboard.php">
                <i class="bi bi-speedometer2 text-warning me-2"></i> لوحة التحكم والإدارة
            </a>
            <div>
                <a href="index.php" class="btn btn-outline-light btn-sm fw-bold me-2"><i class="bi bi-shop me-1"></i> الواجهة الرئيسية</a>
                <a href="logout.php" class="btn btn-danger btn-sm fw-bold"><i class="bi bi-box-arrow-right me-1"></i> خروج</a>
            </div>
        </div>
    </nav>

    <div class="container my-4">

        <!-- التنبيهات -->
        <?php if ($message || isset($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show fw-bold" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php echo $message ? $message : "تم إجراء العملية بنجاح!"; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show fw-bold" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- النموذج الجانبي: إضافة قسم + إضافة منتج -->
            <div class="col-lg-4">
                
                <!-- إضافة قسم -->
                <div class="card card-custom p-3 bg-white mb-4">
                    <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-folder-plus text-primary me-2"></i>إضافة قسم جديد</h6>
                    <form action="admin_dashboard.php" method="POST" class="d-flex gap-2">
                        <input type="text" name="category_name" class="form-control form-control-sm" placeholder="اسم القسم" required>
                        <button type="submit" name="add_category" class="btn btn-primary btn-sm px-3 fw-bold">إضافة</button>
                    </form>
                </div>

                <!-- إضافة منتج -->
                <div class="card card-custom p-4 bg-white">
                    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-plus-circle-fill text-success me-2"></i>إضافة منتج جديد</h5>
                    <form action="admin_dashboard.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">اسم المنتج *</label>
                            <input type="text" name="name" class="form-control" placeholder="أدخلي اسم المنتج" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">القسم</label>
                            <select name="category_id" class="form-select">
                                <option value="0">بدون قسم</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold">السعر ($) *</label>
                                <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold">المخزون *</label>
                                <input type="number" name="stock" class="form-control" value="10" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">رابط الصورة (URL)</label>
                            <input type="url" name="image" class="form-control" placeholder="https://example.com/image.jpg">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">الوصف</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="وصف قصير للمنتج..."></textarea>
                        </div>
                        <button type="submit" name="add_product" class="btn btn-success w-100 fw-bold"><i class="bi bi-check-lg me-1"></i> إضافة المنتج</button>
                    </form>
                </div>
            </div>

            <!-- جدول عرض و إدارة المنتجات -->
            <div class="col-lg-8">
                <div class="card card-custom p-4 bg-white">
                    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-box-seam text-warning me-2"></i>إدارة المنتجات المتاحة</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>الصورة</th>
                                    <th>اسم المنتج</th>
                                    <th>القسم</th>
                                    <th>السعر</th>
                                    <th>الكمية</th>
                                    <th class="text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($products_result && $products_result->num_rows > 0): ?>
                                    <?php while ($row = $products_result->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <img src="<?php echo !empty($row['image']) ? htmlspecialchars($row['image']) : 'https://images.unsplash.com/photo-1526738549149-8e07eca6c147?w=500&fit=crop'; ?>" 
                                                     class="table-img" 
                                                     onerror="this.src='https://images.unsplash.com/photo-1526738549149-8e07eca6c147?w=500&fit=crop';">
                                            </td>
                                            <td>
                                                <strong class="d-block text-dark"><?php echo htmlspecialchars($row['name']); ?></strong>
                                            </td>
                                            <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($row['category_name'] ?? 'عام'); ?></span></td>
                                            <td class="fw-bold text-success">$<?php echo number_format($row['price'], 2); ?></td>
                                            <td><span class="badge bg-secondary"><?php echo intval($row['stock_quantity'] ?? $row['quantity'] ?? $row['stock'] ?? 0); ?></span></td>
                                            <td class="text-center">
                                                <!-- زر تعديل يتفتح Modal -->
                                                <button class="btn btn-outline-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <!-- زر الحذف -->
                                                <a href="admin_dashboard.php?delete=<?php echo $row['id']; ?>" 
                                                   class="btn btn-outline-danger btn-sm" 
                                                   onclick="return confirm('هل أنتِ متأكدة من حذف هذا المنتج؟');">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>

                                        <!-- مودال التعديل -->
                                        <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold">تعديل المنتج #<?php echo $row['id']; ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="admin_dashboard.php" method="POST">
                                                        <div class="modal-body text-start">
                                                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold">اسم المنتج</label>
                                                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold">القسم</label>
                                                                <select name="category_id" class="form-select">
                                                                    <option value="0">بدون قسم</option>
                                                                    <?php foreach ($categories as $cat): ?>
                                                                        <option value="<?php echo $cat['id']; ?>" <?php echo $row['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                                                            <?php echo htmlspecialchars($cat['name']); ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            <div class="row g-2 mb-3">
                                                                <div class="col-6">
                                                                    <label class="form-label small fw-bold">السعر ($)</label>
                                                                    <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $row['price']; ?>" required>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="form-label small fw-bold">الكمية</label>
                                                                    <input type="number" name="stock" class="form-control" value="<?php echo intval($row['stock_quantity'] ?? $row['quantity'] ?? $row['stock'] ?? 0); ?>" required>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold">رابط الصورة (URL)</label>
                                                                <input type="url" name="image" class="form-control" value="<?php echo htmlspecialchars($row['image'] ?? ''); ?>">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold">الوصف</label>
                                                                <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($row['description'] ?? ''); ?></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                                                            <button type="submit" name="update_product" class="btn btn-primary btn-sm fw-bold">حفظ التغييرات</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">لا توجد منتجات مسجلة حتى الآن.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>