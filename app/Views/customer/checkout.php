<?php
declare(strict_types=1);
use App\Support\Cart;
$e = static fn ($v): string => htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$money = static fn ($v): string => number_format((float) $v, 0, ',', '.') . ' ₫';
?>
<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Xác nhận đơn hàng – 3D Model Shop</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class="bg-light">
<nav class="navbar navbar-dark bg-dark"><div class="container"><a class="navbar-brand fw-bold" href="/">3D MODEL SHOP</a><a class="btn btn-outline-light btn-sm" href="/cart">Giỏ hàng</a></div></nav>
<main class="container py-5" style="max-width:850px"><h1 class="h2 fw-bold">Xác nhận đặt hàng</h1><p class="text-secondary">Đơn hàng sẽ gắn với tài khoản đăng nhập. Chưa tích hợp thanh toán hay giao hàng thực tế.</p>
<?php if ($notice): ?><div class="alert alert-warning"><?= $e($notice['message']) ?></div><?php endif; ?>
<div class="card shadow-sm border-0"><div class="card-body">
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Sản phẩm</th><th>Số lượng</th><th class="text-end">Thành tiền</th></tr></thead><tbody>
<?php foreach ($items as $item): ?><tr><td><?= $e($item['name']) ?></td><td><?= (int) $item['quantity'] ?></td><td class="text-end"><?= $money($item['subtotal']) ?></td></tr><?php endforeach; ?>
</tbody></table></div>
<div class="d-flex justify-content-between align-items-center mb-4"><strong>Tổng tiền</strong><strong class="fs-4 text-danger"><?= $money($total) ?></strong></div>
<form method="post" action="/checkout"><input type="hidden" name="csrf_token" value="<?= $e(Cart::csrfToken()) ?>"><button class="btn btn-success" type="submit">Xác nhận đặt hàng</button><a class="btn btn-outline-secondary ms-2" href="/cart">Quay lại giỏ hàng</a></form>
</div></div></main></body></html>
