<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Database;
use PDO;
use Throwable;

final class CatalogController
{
    public function categories(): void
    {
        $categories = [];
        $databaseError = false;
        try {
            $pdo = Database::connect();
            $categories = $pdo->query(
                'SELECT c.id, c.name, COUNT(p.id) AS product_count
                 FROM categories c
                 LEFT JOIN products p ON p.category_id = c.id
                 GROUP BY c.id, c.name
                 ORDER BY c.id'
            )->fetchAll();
        } catch (Throwable $exception) {
            error_log($exception->getMessage());
            $databaseError = true;
            http_response_code(503);
        }
        require dirname(__DIR__) . '/Views/customer/categories.php';
    }

    public function products(): void
    {
        $categories = [];
        $products = [];
        $databaseError = false;

        $rawCategory = $_GET['category'] ?? null;
        $selectedCategory = null;
        if (is_string($rawCategory) && ctype_digit($rawCategory) && $rawCategory !== '0') {
            $category = filter_var($rawCategory, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($category !== false) {
                $selectedCategory = $category;
            }
        }

        $rawQuery = $_GET['q'] ?? '';
        $query = trim(is_string($rawQuery) ? $rawQuery : '');
        $query = function_exists('mb_substr') ? mb_substr($query, 0, 100) : substr($query, 0, 100);
        $availableOnly = ($_GET['available'] ?? '') === '1';
        $rawSort = $_GET['sort'] ?? 'newest';
        $sort = is_string($rawSort) ? $rawSort : 'newest';
        $sorts = [
            'newest' => 'p.id DESC',
            'price_asc' => 'p.price ASC, p.id DESC',
            'price_desc' => 'p.price DESC, p.id DESC',
            'name' => 'p.name ASC, p.id DESC',
        ];
        if (!isset($sorts[$sort])) {
            $sort = 'newest';
        }

        try {
            $pdo = Database::connect();
            $categories = $pdo->query('SELECT id, name FROM categories ORDER BY id')->fetchAll();
            $sql = 'SELECT p.id, p.name, p.description, p.price, p.stock, p.image,
                           c.name AS category_name
                    FROM products p JOIN categories c ON c.id = p.category_id
                    WHERE 1 = 1';
            $params = [];
            if ($selectedCategory !== null) {
                $sql .= ' AND p.category_id = :category_id';
                $params['category_id'] = $selectedCategory;
            }
            if ($query !== '') {
                $sql .= ' AND p.name LIKE :q';
                // Wildcards are escaped so user input remains a literal text search.
                $escapedQuery = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $query);
                $sql .= " ESCAPE '!'";
                $params['q'] = '%' . $escapedQuery . '%';
            }
            if ($availableOnly) {
                $sql .= ' AND p.stock > 0';
            }
            $sql .= ' ORDER BY ' . $sorts[$sort] . ' LIMIT 48';
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $products = $stmt->fetchAll();
        } catch (Throwable $exception) {
            error_log($exception->getMessage());
            $databaseError = true;
            http_response_code(503);
        }
        require dirname(__DIR__) . '/Views/customer/products.php';
    }
}
