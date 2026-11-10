<?php
declare(strict_types=1);
$e = static fn ($v): string => htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$money = static fn ($v): string => number_format((float) $v, 0, ',', '.') . ' ₫';
?>
<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Đơn hàng #<?= (int) $order['id'] ?> – 3D Model Shop</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="bg-light">
<nav class="navbar navbar-dark bg-dark"><div class="container"><a href="/" class="navbar-brand fw-bold">3D MODEL SHOP</a><a class="btn btn-outline-light btn-sm" href="/orders">Đơn hàng của tôi</a></div></nav>
<main class="container py-5" style="max-width:900px"><h1 class="h2 fw-bold">Đơn hàng #<?= (int) $order['id'] ?></h1>
<?php if ($notice): ?><div class="alert alert-success" role="status"><?= $e($notice['message']) ?></div><?php endif; ?>
<p>Ngày đặt: <?= $e($order['created_at']) ?> · Trạng thái: <strong><?= $e($order['status']) ?></strong></p>
<div class="card border-0 shadow-sm"><div class="card-body"><div class="table-responsive"><table class="table"><thead><tr><th>Sản phẩm</th><th>Số lượng</th><th>Đơn giá</th><th class="text-end">Thành tiền</th></tr></thead><tbody>
<?php foreach ($items as $item): ?><tr><td><?= $e($item['name']) ?></td><td><?= (int) $item['quantity'] ?></td><td><?= $money($item['unit_price']) ?></td><td class="text-end"><?= $money((float) $item['unit_price'] * (int) $item['quantity']) ?></td></tr><?php endforeach; ?>
</tbody></table></div><div class="text-end"><strong>Tổng cộng: <span class="text-danger fs-4"><?= $money($order['total']) ?></span></strong></div></div></div>
<a class="btn btn-outline-primary mt-3" href="/orders">Quay lại lịch sử đơn hàng</a></main></body></html>
