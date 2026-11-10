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
        $selectedCategory = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT);
        if ($selectedCategory === false || $selectedCategory === null || $selectedCategory < 1) {
            $selectedCategory = null;
        }
        $query = trim((string) ($_GET['q'] ?? ''));
        $query = mb_substr($query, 0, 100);

        try {
            $pdo = Database::connect();
            $categories = $pdo->query('SELECT id, name FROM categories ORDER BY id')->fetchAll();

            $sql = 'SELECT p.id, p.name, p.description, p.price, p.stock, p.image,
                           c.name AS category_name
                    FROM products p
                    INNER JOIN categories c ON c.id = p.category_id
                    WHERE 1 = 1';
            $params = [];
            if ($selectedCategory !== null) {
                $sql .= ' AND p.category_id = :category';
                $params['category'] = $selectedCategory;
            }
            if ($query !== '') {
                $sql .= ' AND p.name LIKE :search';
                $params['search'] = '%' . $query . '%';
            }
            $sql .= ' ORDER BY p.id DESC LIMIT 24';
            $statement = $pdo->prepare($sql);
            $statement->execute($params);
            $products = $statement->fetchAll();
        } catch (Throwable $exception) {
            error_log($exception->getMessage());
            $databaseError = true;
        }

        require dirname(__DIR__) . '/Views/customer/home.php';
    }
}
