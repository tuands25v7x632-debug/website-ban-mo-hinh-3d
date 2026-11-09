<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Controllers\HomeController;

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if ($path === '/') {
    (new HomeController())->index();
    exit;
}

http_response_code(404);
header('Content-Type: text/plain; charset=UTF-8');
echo '404 - Không tìm thấy trang';
