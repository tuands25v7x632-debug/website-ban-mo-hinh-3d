<?php
declare(strict_types=1);
$e = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$money = static fn ($value): string => number_format((float) $value, 0, ',', '.') . ' ₫';
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>3D Model Shop – Mô hình in 3D sáng tạo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f7f8fb; color:#18243a; }
    .navbar-brand { letter-spacing:.06em; }
    .hero { background:linear-gradient(125deg,#10213e,#234e80 60%,#4c74b0); color:white; border-radius:22px; }
    .hero-mark { font-size:clamp(5rem,12vw,9rem); line-height:1; }
    .category-tile { text-decoration:none; color:inherit; transition:transform .2s; }
    .category-tile:hover { transform:translateY(-3px); }
    .product-art { min-height:190px; background:linear-gradient(135deg,#e9f0fc,#d7e5ff); display:flex; align-items:center; justify-content:center; font-size:5rem; }
    .product-card { border:0; overflow:hidden; transition:transform .2s,box-shadow .2s; }
    .product-card:hover { transform:translateY(-4px); box-shadow:0 12px 25px rgba(16,33,62,.12)!important; }
    .product-card .card-title { min-height:3rem; }
  </style>
</head>
<body>
<header>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand fw-bold" href="/">3D MODEL SHOP</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Mở menu"><span class="navbar-toggler-icon"></span></button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav ms-auto gap-lg-3">
          <li class="nav-item"><a class="nav-link active" href="/">Trang chủ</a></li>
          <li class="nav-item"><a class="nav-link" href="#categories">Danh mục</a></li>
          <li class="nav-item"><a class="nav-link" href="#products">Sản phẩm</a></li>
          <li class="nav-item"><a class="nav-link" href="#about">Giới thiệu</a></li>
        </ul>
      </div>
    </div>
  </nav>
</header>
<main>
  <section class="container py-4 py-lg-5">
    <div class="hero p-4 p-md-5">
      <div class="row align-items-center g-4">
        <div class="col-lg-8">
          <span class="badge bg-light text-dark mb-3">Mô hình in 3D • Sáng tạo không giới hạn</span>
          <h1 class="display-5 fw-bold mb-3">Biến ý tưởng thành những mô hình độc đáo</h1>
          <p class="lead opacity-75">Khám phá mô hình nhân vật, xe và đồ trang trí được thiết kế cho người yêu công nghệ in 3D.</p>
          <a href="#products" class="btn btn-warning btn-lg fw-semibold mt-2">Khám phá sản phẩm</a>
        </div>
        <div class="col-lg-4 text-center" aria-hidden="true"><div class="hero-mark">🧩</div></div>
      </div>
    </div>
  </section>
  <section class="container pb-4" id="categories">
    <div class="d-flex align-items-end justify-content-between mb-3">
      <div><p class="text-primary fw-semibold mb-1">Khám phá</p><h2 class="h3 fw-bold mb-0">Danh mục mô hình 3D</h2></div>
      <a href="/#products" class="text-decoration-none">Tất cả sản phẩm</a>
    </div>
    <div class="row g-3">
      <?php foreach ($categories as $category): ?>
        <div class="col-sm-6 col-lg-4">
          <a class="category-tile d-block card border-0 shadow-sm p-4 h-100" href="/?category=<?= (int) $category['id'] ?>#products">
            <div class="fs-2 mb-2">📦</div>
            <h3 class="h5 mb-1"><?= $e($category['name']) ?></h3>
            <span class="text-secondary small">Xem sản phẩm →</span>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
  <section class="container py-4" id="products">
    <div class="mb-3">
      <p class="text-primary fw-semibold mb-1">Cửa hàng</p><h2 class="h3 fw-bold">Sản phẩm của chúng tôi</h2>
    </div>
    <form class="row g-2 mb-4" action="/" method="get">
      <div class="col-md-6"><label class="visually-hidden" for="search">Tìm sản phẩm</label><input id="search" type="search" class="form-control" name="q" placeholder="Tìm theo tên sản phẩm..." value="<?= $e($query) ?>" maxlength="100"></div>
      <div class="col-md-4"><label class="visually-hidden" for="category">Danh mục</label><select id="category" class="form-select" name="category"><option value="">Tất cả danh mục</option><?php foreach ($categories as $category): ?><option value="<?= (int) $category['id'] ?>" <?= $selectedCategory === (int) $category['id'] ? 'selected' : '' ?>><?= $e($category['name']) ?></option><?php endforeach; ?></select></div>
      <div class="col-md-2"><button class="btn btn-primary w-100" type="submit">Tìm kiếm</button></div>
    </form>
    <?php if ($databaseError): ?>
      <div class="alert alert-warning" role="alert">Chưa kết nối được cơ sở dữ liệu. Hãy kiểm tra file config/database.php và máy chủ MySQL.</div>
    <?php elseif (!$products): ?>
      <div class="alert alert-info">Chưa tìm thấy sản phẩm phù hợp. <a href="/">Xem tất cả sản phẩm</a>.</div>
    <?php else: ?>
      <div class="row g-4">
        <?php foreach ($products as $product): ?>
          <div class="col-sm-6 col-lg-4">
            <article class="card product-card shadow-sm h-100">
              <div class="product-art">
                <?php if (!empty($product['image'])): ?>
                  <img src="/uploads/<?= $e(basename((string) $product['image'])) ?>" class="w-100 h-100 object-fit-cover" style="max-height:210px" alt="<?= $e($product['name']) ?>">
                <?php else: ?>
                  <span aria-hidden="true">🧸</span>
                <?php endif; ?>
              </div>
              <div class="card-body d-flex flex-column">
                <span class="text-primary small fw-semibold mb-2"><?= $e($product['category_name']) ?></span>
                <h3 class="card-title h5"><a class="text-decoration-none text-dark" href="/products/<?= (int) $product['id'] ?>"><?= $e($product['name']) ?></a></h3>
                <p class="text-secondary small flex-grow-1"><?= $e($product['description'] ?? '') ?></p>
                <div class="d-flex align-items-center justify-content-between mt-2">
                  <strong class="fs-5 text-danger"><?= $money($product['price']) ?></strong>
                  <span class="badge <?= (int) $product['stock'] > 0 ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= (int) $product['stock'] > 0 ? 'Còn hàng' : 'Hết hàng' ?></span>
                </div>
                <a class="btn btn-outline-primary mt-3" href="/products/<?= (int) $product['id'] ?>">Xem chi tiết sản phẩm</a>
              </div>
            </article>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
  <section class="container py-4 pb-5" id="about"><div class="p-4 bg-white rounded-4 border"><h2 class="h4 fw-bold">Về 3D Model Shop</h2><p class="text-secondary mb-0">Dự án nhóm môn CT275 – Công nghệ Web. Website sử dụng PHP, PDO, MySQL, Composer và Bootstrap 5. Hiện tại đã có danh mục, tìm kiếm, danh sách và chi tiết sản phẩm; chức năng giỏ hàng và đặt hàng sẽ được phát triển tiếp.</p></div></section>
</main>
<footer class="bg-dark text-white-50 py-4"><div class="container d-flex flex-wrap justify-content-between gap-2"><span class="text-white fw-bold">3D MODEL SHOP</span><small>Đồ án CT275 • Website bán mô hình in 3D</small></div></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
