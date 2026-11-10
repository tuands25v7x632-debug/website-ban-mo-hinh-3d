<?php
declare(strict_types=1);
use App\Support\Cart;
use App\Support\Auth;
$e = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$pageTitle = $pageTitle ?? '3D Model Shop';
$activePage = $activePage ?? '';
?>
<!doctype html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $e($pageTitle) ?> – 3D Model Shop</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{background:#f6f8fc;color:#17253b}
.navbar-brand{letter-spacing:.04em}
.page-hero{border-radius:20px;background:linear-gradient(120deg,#10223e,#315c91);color:#fff}
.catalog-art{background:linear-gradient(135deg,#edf3ff,#d9e7ff);min-height:180px;display:flex;align-items:center;justify-content:center;font-size:4.5rem;overflow:hidden}
.catalog-art img{max-height:220px;object-fit:cover;width:100%;height:100%}
.catalog-card{border:0;overflow:hidden;transition:transform .2s,box-shadow .2s}
.catalog-card:hover{transform:translateY(-3px);box-shadow:0 12px 24px rgba(22,45,80,.14)!important}
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
<div class="container">
<a class="navbar-brand fw-bold" href="/">3D MODEL SHOP</a>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#customerNav" aria-controls="customerNav" aria-expanded="false" aria-label="Mở menu"><span class="navbar-toggler-icon"></span></button>
<div class="collapse navbar-collapse" id="customerNav">
<ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
<li class="nav-item"><a class="nav-link <?= $activePage === 'home' ? 'active' : '' ?>" href="/">Trang chủ</a></li>
<li class="nav-item"><a class="nav-link <?= $activePage === 'categories' ? 'active' : '' ?>" href="/categories">Danh mục</a></li>
<li class="nav-item"><a class="nav-link <?= $activePage === 'products' ? 'active' : '' ?>" href="/products">Sản phẩm</a></li>
<li class="nav-item"><a class="nav-link <?= $activePage === 'about' ? 'active' : '' ?>" href="/about">Giới thiệu</a></li>
<li class="nav-item"><a class="nav-link" href="/cart">🛒 Giỏ hàng (<?= Cart::count() ?>)</a></li>
<?php if (Auth::userId() === null): ?>
<li class="nav-item"><a class="nav-link" href="/login">Đăng nhập</a></li>
<li class="nav-item"><a class="nav-link" href="/register">Đăng ký</a></li>
<?php else: ?>
<li class="nav-item"><a class="nav-link" href="/orders">Đơn hàng của tôi</a></li>
<li class="nav-item"><form method="post" action="/logout" class="d-inline"><input type="hidden" name="csrf_token" value="<?= $e(Cart::csrfToken()) ?>"><button class="btn btn-link nav-link" type="submit">Đăng xuất (<?= $e(Auth::name()) ?>)</button></form></li>
<?php endif; ?>
</ul>
</div>
</div>
</nav>
