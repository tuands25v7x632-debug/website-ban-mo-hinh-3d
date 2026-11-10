<?php
declare(strict_types=1);
$pageTitle = 'Danh mục mô hình 3D';
$activePage = 'categories';
require __DIR__ . '/partials/header.php';
?>
<main class="container py-4 py-lg-5">
<section class="page-hero p-4 p-lg-5 mb-4">
<p class="text-white-50 fw-semibold mb-2">KHÁM PHÁ THEO CHỦ ĐỀ</p>
<h1 class="display-6 fw-bold">Danh mục mô hình in 3D</h1>
<p class="lead mb-0">Tìm nhanh mô hình nhân vật, mô hình xe và phụ kiện trang trí phù hợp với sở thích của bạn.</p>
</section>
<?php if ($databaseError): ?>
<div class="alert alert-warning">Không thể tải danh mục từ MySQL. Vui lòng kiểm tra kết nối cơ sở dữ liệu.</div>
<?php elseif (!$categories): ?>
<div class="alert alert-info">Cửa hàng chưa có danh mục nào.</div>
<?php else: ?>
<div class="row g-4">
<?php
$icons = ['🧑‍🚀', '🏎️', '🪴', '🎨', '🧩', '📦'];
foreach ($categories as $index => $category):
?>
<div class="col-md-6 col-lg-4">
<a href="/products?category=<?= (int) $category['id'] ?>" class="text-decoration-none text-dark">
<article class="card catalog-card shadow-sm h-100">
<div class="catalog-art" aria-hidden="true"><?= $icons[$index % count($icons)] ?></div>
<div class="card-body p-4">
<h2 class="h4 fw-bold"><?= $e($category['name']) ?></h2>
<p class="text-secondary mb-3"><?= (int) $category['product_count'] ?> sản phẩm trong danh mục</p>
<span class="text-primary fw-semibold">Xem sản phẩm →</span>
</div></article>
</a></div>
<?php endforeach; ?>
</div>
<?php endif; ?>
<div class="text-center mt-5"><a href="/products" class="btn btn-outline-primary btn-lg">Xem tất cả sản phẩm</a></div>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
