<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Database;
use Throwable;

final class HomeController
{
    public function index(): void
    {
        $dbStatus = 'Chưa kết nối';
        $dbName = 'Chưa xác định';

        try {
            $pdo = Database::connect();
            $dbName = (string) $pdo->query('SELECT DATABASE()')->fetchColumn();
            $dbStatus = 'Kết nối thành công';
        } catch (Throwable $exception) {
            // Không hiện chi tiết lỗi kết nối chứa thông tin cấu hình cho người dùng.
            error_log($exception->getMessage());
            $dbStatus = 'Chưa kết nối (xem hướng dẫn cấu hình MySQL)';
        }

        require dirname(__DIR__) . '/Views/customer/home.php';
    }
}
