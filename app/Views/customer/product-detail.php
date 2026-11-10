<?php
declare(strict_types=1);
use App\Support\Cart;
$e = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$money = static fn ($value): string => number_format((float) $value, 0, ',', '.') . ' ₫';
$inStock = (int) $product['stock'] > 0;
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $e($product['name']) ?> – 3D Model Shop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{background:#f7f8fb;color:#18243a}
    .detail-art{min-height:350px;background:linear-gradient(135deg,#e9f0fc,#d7e5ff);display:flex;align-items:center;justify-content:center;font-size:8rem}
    .detail-art img{max-width:100%;max-height:440px;object-fit:contain}
    .detail-card{border:0;border-radius:20px;overflow:hidden}
  </style>
</head>
<body>
  <nav class="navbar navbar-expand navbar-dark bg-dark">
    <div class="container"><a class="navbar-brand fw-bold" href="/">3D MODEL SHOP</a><div class="d-flex gap-3"><a class="nav-link text-light" href="/#products">Sản phẩm</a><a class="nav-link text-light" href="/cart">🛒 Giỏ hàng (<?= Cart::count() ?>)</a></div></div>
  </nav>
  <main class="container py-4 py-lg-5">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="/?category=<?= (int) $product['category_id'] ?>#products"><?= $e($product['category_name']) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= $e($product['name']) ?></li>
      </ol>
    </nav>
    <div class="card shadow-sm detail-card">
      <div class="row g-0">
        <div class="col-lg-6">
          <div class="detail-art h-100">
            <?php if (!empty($product['image'])): ?>
              <img src="/uploads/<?= rawurlencode(basename((string) $product['image'])) ?>" alt="<?= $e($product['name']) ?>">
            <?php else: ?>
              <span aria-label="Ảnh minh họa mô hình 3D" role="img">🧸</span>
            <?php endif; ?>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card-body p-4 p-lg-5">
            <span class="text-primary fw-semibold"><?= $e($product['category_name']) ?></span>
            <h1 class="display-6 fw-bold mt-2"><?= $e($product['name']) ?></h1>
            <p class="display-6 fw-bold text-danger"><?= $money($product['price']) ?></p>
            <?php if ($inStock): ?>
              <span class="badge text-bg-success mb-3">Còn hàng</span>
              <p class="text-secondary">Số lượng trong kho: <strong><?= (int) $product['stock'] ?></strong></p>
            <?php else: ?>
              <span class="badge text-bg-secondary mb-3">Hết hàng</span>
              <p class="text-secondary">Sản phẩm tạm hết hàng.</p>
            <?php endif; ?>
            <h2 class="h5 mt-3">Mô tả sản phẩm</h2>
            <p class="text-secondary"><?= nl2br($e($product['description'] ?: 'Chưa có mô tả cho sản phẩm này.')) ?></p>
            <div class="d-flex gap-2 flex-wrap mt-4 align-items-end">
              <?php if ($inStock): ?>
              <form action="/cart/add" method="post" class="d-flex flex-wrap gap-2 align-items-end">
                <input type="hidden" name="csrf_token" value="<?= $e(Cart::csrfToken()) ?>">
                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                <div><label for="quantity" class="form-label small">Số lượng</label><input id="quantity" class="form-control" type="number" name="quantity" min="1" max="<?= min(99, (int) $product['stock']) ?>" value="1" style="width:100px" required></div>
                <button class="btn btn-primary" type="submit">🛒 Thêm vào giỏ hàng</button>
              </form>
              <?php else: ?>
              <button class="btn btn-secondary" type="button" disabled>Đã hết hàng</button>
              <?php endif; ?>
              <a class="btn btn-outline-primary" href="/#products">Xem sản phẩm khác</a>
            </div>
            <p class="small text-secondary mt-3">Giỏ hàng lưu tạm bằng PHP Session. Sau khi thêm sản phẩm, vào giỏ hàng để đặt hàng.</p>
          </div>
        </div>
      </div>
    </div>
    <?php if ($relatedProducts): ?>
    <section class="mt-5" aria-label="Sản phẩm liên quan">
      <h2 class="h4 fw-bold mb-3">Sản phẩm cùng danh mục</h2>
      <div class="row g-3">
        <?php foreach ($relatedProducts as $item): ?>
          <div class="col-md-4">
            <a href="/products/<?= (int) $item['id'] ?>" class="card shadow-sm border-0 text-decoration-none text-dark h-100">
              <div class="card-body"><h3 class="h6"><?= $e($item['name']) ?></h3><strong class="text-danger"><?= $money($item['price']) ?></strong></div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>
  </main>
  <footer class="bg-dark text-white-50 text-center py-4">3D MODEL SHOP • Dự án CT275</footer>
</body>
</html>
