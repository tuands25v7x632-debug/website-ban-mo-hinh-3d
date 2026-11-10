<?php
declare(strict_types=1);

// HTTP headers bảo vệ cơ bản; chạy localhost HTTP chỉ phục vụ học và kiểm thử.
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Cache-Control: no-store, max-age=0');
header('Pragma: no-cache');


// Phiên làm việc cho giỏ hàng. Cookie chỉ truy cập qua HTTP; production cần HTTPS.
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_set_cookie_params([
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
    'path' => '/',
]);
session_start();

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\CartController;
use App\Controllers\AuthController;
use App\Controllers\OrderController;

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($path === '/' && $method === 'GET') {
    (new HomeController())->index();
    exit;
}
if (is_string($path) && preg_match('~^/products/([^/]+)/?$~D', $path, $matches) === 1 && $method === 'GET') {
    (new ProductController())->show($matches[1]);
    exit;
}
if ($path === '/cart' && $method === 'GET') {
    (new CartController())->index();
    exit;
}
$cartActions = ['/cart/add' => 'add', '/cart/update' => 'update', '/cart/remove' => 'remove'];
if (is_string($path) && isset($cartActions[$path])) {
    if ($method !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit('405 - Method Not Allowed');
    }
    (new CartController())->{$cartActions[$path]}();
    exit;
}
$auth = new AuthController();
if ($path === '/register') {
    if ($method === 'GET') { $auth->registerForm(); exit; }
    if ($method === 'POST') { $auth->register(); exit; }
}
if ($path === '/login') {
    if ($method === 'GET') { $auth->loginForm(); exit; }
    if ($method === 'POST') { $auth->login(); exit; }
}
if ($path === '/logout') {
    if ($method !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit('405 - Method Not Allowed');
    }
    $auth->logout();
    exit;
}
if ($path === '/checkout') {
    $orders = new OrderController();
    if ($method === 'GET') { $orders->checkout(); exit; }
    if ($method === 'POST') { $orders->place(); exit; }
}
if ($path === '/orders' && $method === 'GET') {
    (new OrderController())->index();
    exit;
}
if (is_string($path) && preg_match('~^/orders/([^/]+)/?$~D', $path, $matches) === 1 && $method === 'GET') {
    (new OrderController())->show($matches[1]);
    exit;
}
if (in_array($path, ['/register', '/login', '/checkout'], true) && !in_array($method, ['GET', 'POST'], true)) {
    http_response_code(405);
    header('Allow: GET, POST');
    exit('405 - Method Not Allowed');
}
http_response_code(404);
require dirname(__DIR__) . '/app/Views/customer/not-found.php';
