<?php
declare(strict_types=1);
$pageTitle = 'Giới thiệu';
$activePage = 'about';
require __DIR__ . '/partials/header.php';
?>
<main class="container py-4 py-lg-5">
<section class="page-hero p-4 p-lg-5 mb-5">
<p class="text-white-50 fw-semibold mb-2">VỀ DỰ ÁN</p>
<h1 class="display-6 fw-bold">3D Model Shop</h1>
<p class="lead mb-0">Website quản lý bán mô hình in 3D – đồ án nhóm môn Công nghệ Web CT275.</p>
</section>
<div class="row g-4 align-items-center mb-5">
<div class="col-lg-7">
<h2 class="h3 fw-bold">Khám phá thế giới mô hình 3D</h2>
<p class="text-secondary">3D Model Shop là website học tập mô phỏng cửa hàng mô hình in 3D. Khách hàng có thể tìm kiếm theo tên, lọc theo danh mục, xem giá bán, số lượng tồn kho và chi tiết mô hình.</p>
<p class="text-secondary">Sau khi đăng ký hoặc đăng nhập, khách hàng có thể tạo đơn hàng thử nghiệm, theo dõi lịch sử và xem chi tiết đơn đã đặt.</p>
<div class="d-flex gap-2 flex-wrap"><a class="btn btn-primary" href="/products">Khám phá sản phẩm</a><a class="btn btn-outline-primary" href="/categories">Xem danh mục</a></div>
</div>
<div class="col-lg-5"><div class="rounded-4 bg-white shadow-sm p-5 text-center"><div style="font-size:6rem" aria-hidden="true">🧩</div><div class="fw-bold fs-4">Mô hình · Sáng tạo · Công nghệ</div></div></div>
</div>
<h2 class="h3 fw-bold mb-4">Quy trình đặt hàng thử nghiệm</h2>
<div class="row g-3 mb-5">
<?php foreach ([['01','Tìm sản phẩm','Xem danh mục, tìm kiếm và xem chi tiết mô hình.'],['02','Thêm vào giỏ','Chọn số lượng sản phẩm phù hợp với tồn kho.'],['03','Xác nhận đơn','Đăng nhập và gửi đơn hàng để lưu trong MySQL.'],['04','Xem lịch sử','Kiểm tra các đơn đã đặt theo tài khoản.']] as $step): ?>
<div class="col-sm-6 col-lg-3"><div class="card border-0 shadow-sm h-100"><div class="card-body p-4"><div class="text-primary fw-bold fs-3 mb-2"><?= $e($step[0]) ?></div><h3 class="h5 fw-bold"><?= $e($step[1]) ?></h3><p class="text-secondary small mb-0"><?= $e($step[2]) ?></p></div></div></div>
<?php endforeach; ?>
</div>
<div class="card border-0 shadow-sm"><div class="card-body p-4 p-lg-5">
<h2 class="h3 fw-bold">Công nghệ xây dựng</h2>
<p class="text-secondary">PHP, PDO, MySQL, Composer PSR-4, kiến trúc MVC đơn giản và Bootstrap 5.</p>

</div></div>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
