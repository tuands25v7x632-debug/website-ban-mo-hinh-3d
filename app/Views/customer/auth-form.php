<?php
declare(strict_types=1);
use App\Support\Cart;
$e = static fn ($v): string => htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= $e($title) ?> – 3D Model Shop</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light"><nav class="navbar navbar-dark bg-dark"><div class="container"><a href="/" class="navbar-brand fw-bold">3D MODEL SHOP</a><a href="/cart" class="btn btn-outline-light btn-sm">Giỏ hàng</a></div></nav>
<main class="container py-5"><div class="card shadow-sm border-0 mx-auto" style="max-width:520px"><div class="card-body p-4 p-md-5"><h1 class="h3 fw-bold mb-4"><?= $e($title) ?></h1>
<?php if ($errors): ?><div class="alert alert-danger" role="alert"><ul class="mb-0"><?php foreach ($errors as $error): ?><li><?= $e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form action="<?= $e($action) ?>" method="post">
<input type="hidden" name="csrf_token" value="<?= $e(Cart::csrfToken()) ?>">
<?php if ($isRegistration): ?><div class="mb-3"><label for="name" class="form-label">Họ tên</label><input id="name" type="text" class="form-control" name="name" maxlength="120" value="<?= $e($old['name'] ?? '') ?>" required autocomplete="name"></div><?php endif; ?>
<div class="mb-3"><label for="email" class="form-label">Email</label><input id="email" type="email" class="form-control" name="email" maxlength="190" value="<?= $e($old['email'] ?? '') ?>" required autocomplete="email"></div>
<div class="mb-3"><label for="password" class="form-label">Mật khẩu</label><input id="password" type="password" class="form-control" name="password" <?= $isRegistration ? 'minlength="8" maxlength="72"' : '' ?> required autocomplete="<?= $isRegistration ? 'new-password' : 'current-password' ?>"></div>
<?php if ($isRegistration): ?><div class="mb-3"><label for="confirm_password" class="form-label">Nhập lại mật khẩu</label><input id="confirm_password" type="password" class="form-control" name="confirm_password" minlength="8" maxlength="72" required autocomplete="new-password"></div><?php endif; ?>
<button class="btn btn-primary w-100" type="submit"><?= $e($submitLabel) ?></button></form>
<p class="text-center mt-3 mb-0"><?php if ($isRegistration): ?>Đã có tài khoản? <a href="/login">Đăng nhập</a><?php else: ?>Chưa có tài khoản? <a href="/register">Đăng ký</a><?php endif; ?></p>
</div></div></main></body></html>
