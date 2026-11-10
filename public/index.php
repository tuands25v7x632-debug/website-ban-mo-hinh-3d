<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Controllers\HomeController;
use App\Controllers\ProductController;

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if ($path === '/') {
    (new HomeController())->index();
    exit;
}
if (is_string($path) && preg_match('~^/products/([^/]+)/?$~D', $path, $matches) === 1) {
    (new ProductController())->show($matches[1]);
    exit;
}

http_response_code(404);
require dirname(__DIR__) . '/app/Views/customer/not-found.php';
