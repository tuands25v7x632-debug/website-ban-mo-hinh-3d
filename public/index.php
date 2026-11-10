<?php
declare(strict_types=1);

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
http_response_code(404);
require dirname(__DIR__) . '/app/Views/customer/not-found.php';
