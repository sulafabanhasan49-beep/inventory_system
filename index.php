<?php
session_start();
require_once 'config/db.php';

// جلب الأقسام للفلترة
$categories_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");

// إعداد الفلترة والبحث
$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT products.*, categories.name AS category_name FROM products LEFT JOIN categories ON products.category_id = categories.id WHERE 1=1";

if ($category_id > 0) {
    $sql .= " AND products.category_id = $category_id";
}

if (!empty($search)) {
    $safe_search = $conn->real_escape_string($search);
    $sql .= " AND (products.name LIKE '%$safe_search%' OR products.description LIKE '%$safe_search%')";
}

$sql .= " ORDER BY products.id DESC";
$products_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المتجر الإلكتروني - إدارة المخزون</title>
    <!-- Bootstrap 5 RTL & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-custom { background-color: #0f172a; }
        .product-card { border: none; border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
        .product-img { height: 220px; object-fit: cover; border-top-left-radius: 12px; border-top-right-radius: 12px; }
    </style>
</head>
<body>

    <!-- الهيدر العلوي -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3 shadow-sm">
        <div class="container px-4">
            <a class="navbar-brand fw-bold fs-4" href="index.php"><i class="bi bi-shop text-warning me-2"></i> متجر المخزون</a>
            <div class="d-flex align-items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="text-light me-3 small"><i class="bi bi-person-circle me-1"></i> أهلاً، <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'المدير'); ?></strong></span>
                    <a href="admin_dashboard.php" class="btn btn-warning btn-sm fw-bold me-2"><i class="bi bi-speedometer2 me-1"></i> لوحة التحكم</a>
                    <a href="logout.php" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right"></i> خروج</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light btn-sm fw-bold"><i class="bi bi-box-arrow-in-right me-1"></i> تسجيل الدخول</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- شريط الفلترة والبحث -->
    <div class="container my-4">
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h2 class="fw-bold text-dark mb-1">المنتجات المتاحة</h2>
                <p class="text-muted small mb-0">تصفحي قائمة المنتجات المتوفرة في المخزون حالياً</p>
            </div>
            <div class="col-md-6 mt-3 mt-md-0">
                <form action="index.php" method="GET" class="d-flex gap-2">
                    <select name="category" class="form-select w-auto">
                        <option value="0">كل الأقسام</option>
                        <?php if ($categories_result && $categories_result->num_rows > 0): ?>
                            <?php while ($cat = $categories_result->fetch_assoc()): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $category_id == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                    <input type="text" name="search" class="form-control" placeholder="ابحثي عن منتج..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary px-3"><i class="bi bi-search"></i></button>
                    <?php if ($category_id > 0 || !empty($search)): ?>
                        <a href="index.php" class="btn btn-outline-secondary" title="إلغاء الفلترة"><i class="bi bi-x-lg"></i></a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- شبكة عرض المنتجات -->
        <div class="row g-4">
            <?php if ($products_result && $products_result->num_rows > 0): ?>
                <?php while ($product = $products_result->fetch_assoc()): ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="card product-card h-100 shadow-sm bg-white">
                            <!-- عرض صورة المنتج -->
                            <img src="<?php echo (!empty($product['image'])) ? htmlspecialchars($product['image']) : 'https://images.unsplash.com/photo-1526738549149-8e07eca6c147?w=500&fit=crop'; ?>" 
                                 class="card-img-top product-img" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 onerror="this.src='https://images.unsplash.com/photo-1526738549149-8e07eca6c147?w=500&fit=crop';">
                            
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-info text-dark rounded-pill px-2 py-1 small">
                                        <?php echo htmlspecialchars($product['category_name'] ?? 'إلكترونيات وأجهزة ذكية'); ?>
                                    </span>
                                    <span class="fw-bold text-success fs-5">$<?php echo number_format($product['price'], 2); ?></span>
                                </div>
                                <h5 class="card-title fw-bold text-dark mb-2"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text text-muted small flex-grow-1">
                                    <?php echo htmlspecialchars(mb_strimwidth($product['description'] ?? 'لا يوجد وصف للمنتج.', 0, 80, '...')); ?>
                                </p>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small text-secondary">المخزون: <strong><?php echo intval($product['stock_quantity'] ?? $product['quantity'] ?? $product['stock'] ?? 0); ?></strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-box-seam display-1 text-muted"></i>
                    <h4 class="mt-3 text-secondary">لم يتم العثور على أي منتجات!</h4>
                    <p class="text-muted">جربي البحث بكلمة أخرى أو اختاري قسم آخر.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>