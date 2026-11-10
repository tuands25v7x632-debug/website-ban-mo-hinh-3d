<?php
declare(strict_types=1);
$pageTitle = 'Tất cả sản phẩm';
$activePage = 'products';
require __DIR__ . '/partials/header.php';
$money = static fn ($value): string => number_format((float) $value, 0, ',', '.') . ' ₫';
?>
<main class="container py-4 py-lg-5">
<section class="page-hero p-4 p-lg-5 mb-4">
<p class="text-white-50 fw-semibold mb-2">CỬA HÀNG 3D MODEL SHOP</p>
<h1 class="display-6 fw-bold">Mô hình in 3D độc đáo</h1>
<p class="lead mb-0">Khám phá sản phẩm, lọc theo danh mục và chọn mô hình yêu thích.</p>
</section>
<div class="card border-0 shadow-sm mb-4"><div class="card-body p-3 p-lg-4">
<form action="/products" method="get" class="row g-3 align-items-end">
<div class="col-lg-4">
<label for="q" class="form-label fw-semibold">Tên sản phẩm</label>
<input id="q" name="q" class="form-control" type="search" value="<?= $e($query) ?>" maxlength="100" placeholder="Ví dụ: phi hành gia">
</div>
<div class="col-md-6 col-lg-3">
<label for="category" class="form-label fw-semibold">Danh mục</label>
<select id="category" name="category" class="form-select">
<option value="">Tất cả danh mục</option>
<?php foreach ($categories as $category): ?>
<option value="<?= (int) $category['id'] ?>" <?= $selectedCategory === (int) $category['id'] ? 'selected' : '' ?>><?= $e($category['name']) ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="col-md-6 col-lg-3">
<label for="sort" class="form-label fw-semibold">Sắp xếp</label>
<select id="sort" name="sort" class="form-select">
<option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
<option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Giá thấp đến cao</option>
<option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Giá cao đến thấp</option>
<option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Tên A–Z</option>
</select>
</div>
<div class="col-md-6 col-lg-2"><button class="btn btn-primary w-100" type="submit">Tìm kiếm</button></div>
<div class="col-12 d-flex justify-content-between flex-wrap gap-2">
<div class="form-check"><input type="checkbox" class="form-check-input" id="available" name="available" value="1" <?= $availableOnly ? 'checked' : '' ?>><label for="available" class="form-check-label">Chỉ sản phẩm còn hàng</label></div>
<a href="/products" class="small text-decoration-none">Xóa bộ lọc</a>
</div>
</form></div></div>
<?php if ($databaseError): ?>
<div class="alert alert-warning">Chưa tải được sản phẩm từ MySQL. Vui lòng kiểm tra máy chủ và cấu hình database.</div>
<?php elseif (!$products): ?>
<div class="alert alert-info">Không tìm thấy sản phẩm phù hợp. <a href="/products">Hiển thị tất cả sản phẩm</a>.</div>
<?php else: ?>
<div class="d-flex align-items-center justify-content-between mb-3">
<h2 class="h4 fw-bold mb-0">Kết quả tìm kiếm</h2>
<span class="text-secondary small">Đang hiển thị <?= count($products) ?> sản phẩm (tối đa 48)</span>
</div>
<div class="row g-4">
<?php foreach ($products as $product): ?>
<div class="col-sm-6 col-lg-4">
<article class="card catalog-card h-100 shadow-sm">
<div class="catalog-art">
<?php if (!empty($product['image'])): ?>
<img src="/uploads/<?= rawurlencode(basename((string) $product['image'])) ?>" alt="<?= $e($product['name']) ?>">
<?php else: ?><span aria-hidden="true">🧸</span><?php endif; ?>
</div>
<div class="card-body d-flex flex-column p-4">
<p class="small text-primary fw-semibold mb-2"><?= $e($product['category_name']) ?></p>
<h3 class="h5"><a class="text-decoration-none text-dark" href="/products/<?= (int) $product['id'] ?>"><?= $e($product['name']) ?></a></h3>
<p class="small text-secondary flex-grow-1"><?= $e($product['description'] ?? '') ?></p>
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
<strong class="text-danger fs-5"><?= $money($product['price']) ?></strong>
<span class="badge <?= (int) $product['stock'] > 0 ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= (int) $product['stock'] > 0 ? 'Còn hàng' : 'Hết hàng' ?></span>
</div>
<a href="/products/<?= (int) $product['id'] ?>" class="btn btn-outline-primary mt-3">Xem chi tiết</a>
</div></article>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
