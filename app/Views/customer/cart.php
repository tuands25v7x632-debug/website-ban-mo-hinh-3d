<?php
declare(strict_types=1);
use App\Support\Cart;
$e = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$money = static fn ($value): string => number_format((float) $value, 0, ',', '.') . ' ₫';
$csrfToken = Cart::csrfToken();
?>
<!doctype html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Giỏ hàng – 3D Model Shop</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
 body{background:#f7f8fb;color:#18243a}
 .product-icon{background:#e6efff;border-radius:12px;min-width:66px;height:66px;display:flex;align-items:center;justify-content:center;font-size:2rem}
</style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark"><div class="container"><a class="navbar-brand fw-bold" href="/">3D MODEL SHOP</a><a class="btn btn-outline-light btn-sm" href="/#products">Tiếp tục mua hàng</a></div></nav>
<main class="container py-4 py-lg-5">
<div class="d-flex justify-content-between flex-wrap align-items-center gap-2 mb-4"><div><h1 class="h2 fw-bold mb-1">Giỏ hàng của bạn</h1><p class="text-secondary mb-0">Sản phẩm được lưu tạm trong phiên làm việc (PHP Session).</p></div><span class="badge text-bg-primary fs-6"><?= Cart::count() ?> sản phẩm</span></div>
<?php if ($notice): ?>
<div class="alert alert-<?= in_array($notice['type'], ['success','warning','danger','info'], true) ? $e($notice['type']) : 'info' ?>" role="status"><?= $e($notice['message']) ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger">Không thể lấy thông tin sản phẩm từ MySQL. Vui lòng kiểm tra máy chủ cơ sở dữ liệu rồi tải lại trang.</div>
<?php elseif (!$items): ?>
<div class="card shadow-sm border-0"><div class="card-body py-5 text-center"><div style="font-size:4rem" aria-hidden="true">🛒</div><h2 class="h4">Giỏ hàng đang trống</h2><p class="text-secondary">Hãy chọn mô hình 3D yêu thích để thêm vào giỏ hàng.</p><a href="/#products" class="btn btn-primary">Khám phá sản phẩm</a></div></div>
<?php else: ?>
<div class="row g-4">
<div class="col-lg-8">
<?php foreach ($items as $item): ?>
<article class="card shadow-sm border-0 mb-3">
<div class="card-body p-3 p-md-4">
<div class="d-flex align-items-start gap-3 mb-3"><div class="product-icon" aria-hidden="true">🧸</div><div class="flex-grow-1"><h2 class="h5 mb-1"><a class="text-decoration-none text-dark" href="/products/<?= (int) $item['id'] ?>"><?= $e($item['name']) ?></a></h2><span class="text-danger fw-bold"><?= $money($item['price']) ?></span></div></div>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
<form method="post" action="/cart/update" class="d-flex align-items-end gap-2 flex-wrap">
<input type="hidden" name="csrf_token" value="<?= $e($csrfToken) ?>">
<input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
<div><label for="qty-<?= (int) $item['id'] ?>" class="form-label small mb-1">Số lượng</label><input id="qty-<?= (int) $item['id'] ?>" type="number" class="form-control" style="width:100px" min="0" max="<?= min(99, (int) $item['stock']) ?>" name="quantity" value="<?= (int) $item['quantity'] ?>" required></div>
<button type="submit" class="btn btn-outline-primary">Cập nhật</button>
</form>
<form method="post" action="/cart/remove">
<input type="hidden" name="csrf_token" value="<?= $e($csrfToken) ?>">
<input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
<button type="submit" class="btn btn-outline-danger">Xóa</button>
</form>
</div>
<div class="d-flex justify-content-between mt-3 pt-3 border-top"><span class="text-secondary">Tạm tính (<?= (int) $item['quantity'] ?> sản phẩm)</span><strong><?= $money($item['subtotal']) ?></strong></div>
<?php if ($item['exceeds_stock']): ?><p class="text-danger small mt-2 mb-0">Số lượng đang chọn vượt quá tồn kho (còn <?= (int) $item['stock'] ?>). Vui lòng cập nhật.</p><?php endif; ?>
</div></article>
<?php endforeach; ?>
</div>
<aside class="col-lg-4"><div class="card border-0 shadow-sm"><div class="card-body p-4"><h2 class="h5 fw-bold">Tóm tắt giỏ hàng</h2><hr><div class="d-flex justify-content-between mb-3"><span>Tổng cộng</span><strong class="text-danger fs-4"><?= $money($total) ?></strong></div><p class="text-secondary small">Giá và tồn kho được đọc lại từ MySQL khi xem giỏ hàng. Chưa bao gồm phí vận chuyển.</p><a class="btn btn-success w-100" href="/checkout">Tiến hành đặt hàng</a><a href="/#products" class="btn btn-outline-primary w-100 mt-2">Tiếp tục mua sắm</a></div></div></aside>
</div>
<?php endif; ?>
</main>
<footer class="text-center text-secondary small py-4">3D Model Shop – Đồ án môn CT275</footer>
</body></html>
