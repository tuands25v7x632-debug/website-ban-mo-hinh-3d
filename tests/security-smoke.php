<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Support\Auth;
use App\Support\Cart;

ini_set('session.use_strict_mode', '1');
session_start();
ob_start(); // Không gửi output trước session_regenerate_id() trong bài test.

function verify(bool $condition, string $label): void
{
    if (!$condition) {
        fwrite(STDERR, "[FAIL] {$label}\n");
        exit(1);
    }
    echo "[PASS] {$label}\n";
}

$_SESSION = [];
verify(Cart::all() === [], 'Giỏ hàng ban đầu trống');
Cart::set(1, 2);
Cart::set(2, 1);
verify(Cart::count() === 3, 'Đếm đúng tổng số lượng');
Cart::set(1, 0);
verify(Cart::all() === [2 => 1], 'Xóa đúng sản phẩm');
$_SESSION['cart']['abc'] = 10;
$_SESSION['cart'][3] = -2;
verify(Cart::all() === [2 => 1], 'Lọc các mục session không hợp lệ');

$token = Cart::csrfToken();
verify(strlen($token) === 64 && Cart::validCsrf($token), 'CSRF token hợp lệ');
verify(!Cart::validCsrf('wrong-token') && !Cart::validCsrf(null), 'Từ chối CSRF sai/thiếu');

Auth::login(['id' => 42, 'name' => 'Khách thử', 'role' => 'customer']);
verify(Auth::userId() === 42 && Auth::isCustomer(), 'Đăng nhập session khách hàng');
verify(!Cart::validCsrf($token), 'Đổi CSRF token sau khi đăng nhập');
Auth::logout();
verify(Auth::userId() === null, 'Đăng xuất xóa danh tính');
verify(!Cart::validCsrf(Cart::csrfToken() === $token ? $token : 'wrong-token'), 'CSRF sai bị từ chối sau đăng xuất');

echo "Các kiểm thử đơn vị PHP Session đã hoàn thành.\n";
