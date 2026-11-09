<?php
declare(strict_types=1);
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>3D Model Shop | CT275</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">3D MODEL SHOP</a>
            <span class="navbar-text text-white-50">Đồ án CT275</span>
        </div>
    </nav>
</header>
<main class="container py-5">
    <div class="text-center mb-4">
        <h1 class="fw-bold">Website quản lý bán mô hình in 3D</h1>
        <p class="text-secondary">Bước 1 – Khởi tạo môi trường PHP, MVC và PDO</p>
    </div>
    <section class="card shadow-sm mx-auto" style="max-width:720px" aria-labelledby="systemTitle">
        <div class="card-body p-4">
            <h2 id="systemTitle" class="h4">Kiểm tra hệ thống</h2>
            <hr>
            <dl class="row mb-0">
                <dt class="col-sm-5">PHP</dt><dd class="col-sm-7">Đang hoạt động (<?= $escape(PHP_VERSION) ?>)</dd>
                <dt class="col-sm-5">Composer PSR-4</dt><dd class="col-sm-7">Đã tải HomeController</dd>
                <dt class="col-sm-5">MySQL / PDO</dt><dd class="col-sm-7"><?= $escape($dbStatus) ?></dd>
                <dt class="col-sm-5">Database</dt><dd class="col-sm-7"><?= $escape($dbName) ?></dd>
            </dl>
            <div class="alert alert-info mt-3 mb-0">Trang thử nghiệm ban đầu. Danh sách sản phẩm và giỏ hàng sẽ được triển khai ở các bước tiếp theo.</div>
        </div>
    </section>
</main>
<footer class="text-center text-secondary small py-4">3D Model Shop – Dự án môn Công nghệ Web</footer>
</body>
</html>
