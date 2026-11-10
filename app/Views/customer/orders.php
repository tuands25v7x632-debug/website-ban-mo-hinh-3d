<?php
declare(strict_types=1);
$e = static fn ($v): string => htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$money = static fn ($v): string => number_format((float) $v, 0, ',', '.') . ' ₫';
?>
<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Đơn hàng của tôi – 3D Model Shop</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="bg-light">
<nav class="navbar navbar-dark bg-dark"><div class="container"><a href="/" class="navbar-brand fw-bold">3D MODEL SHOP</a><a href="/cart" class="btn btn-outline-light btn-sm">Giỏ hàng</a></div></nav>
<main class="container py-5"><h1 class="h2 fw-bold mb-4">Đơn hàng của tôi</h1><?php if ($notice): ?><div class="alert alert-success" role="status"><?= $e($notice['message']) ?></div><?php endif; ?>
<?php if (!$orders): ?><div class="alert alert-info">Bạn chưa có đơn hàng. <a href="/#products">Mua sắm ngay</a>.</div><?php else: ?>
<div class="table-responsive bg-white rounded shadow-sm p-3"><table class="table table-striped align-middle"><thead><tr><th>Mã đơn</th><th>Ngày đặt</th><th>Trạng thái</th><th>Tổng tiền</th><th></th></tr></thead><tbody>
<?php foreach ($orders as $order): ?><tr><td>#<?= (int) $order['id'] ?></td><td><?= $e($order['created_at']) ?></td><td><?= $e($order['status']) ?></td><td class="text-danger fw-semibold"><?= $money($order['total']) ?></td><td><a href="/orders/<?= (int) $order['id'] ?>" class="btn btn-outline-primary btn-sm">Chi tiết</a></td></tr><?php endforeach; ?>
</tbody></table></div><?php endif; ?></main></body></html>
