<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Database;
use Throwable;

final class HomeController
{
    public function index(): void
    {
        $categories = [];
        $products = [];
        $databaseError = false;

        try {
            $pdo = Database::connect();
            $categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
            $products = $pdo->query(
                'SELECT p.id, p.name, p.description, p.price, p.stock, p.image, c.name AS category_name
                 FROM products p INNER JOIN categories c ON c.id = p.category_id
                 ORDER BY p.id DESC LIMIT 12'
            )->fetchAll();
        } catch (Throwable $exception) {
            error_log($exception->getMessage());
            $databaseError = true;
        }

        require dirname(__DIR__) . '/Views/customer/home.php';
    }
}
