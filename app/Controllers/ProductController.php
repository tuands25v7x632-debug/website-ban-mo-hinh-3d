<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Database;
use PDO;
use Throwable;

final class ProductController
{
    public function show(string $rawId): void
    {
        // Chỉ chấp nhận ID nguyên dương, tránh ép kiểu chuỗi không hợp lệ.
        if (!ctype_digit($rawId) || $rawId === '0' || strlen($rawId) > 10) {
            $this->notFound();
            return;
        }
        $id = filter_var($rawId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id === false) {
            $this->notFound();
            return;
        }

        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare(
                'SELECT p.id, p.category_id, p.name, p.description, p.price, p.stock, p.image,
                        c.name AS category_name
                 FROM products p
                 INNER JOIN categories c ON c.id = p.category_id
                 WHERE p.id = :id
                 LIMIT 1'
            );
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $product = $stmt->fetch();

            if (!$product) {
                $this->notFound();
                return;
            }

            $relatedStmt = $pdo->prepare(
                'SELECT id, name, price FROM products
                 WHERE category_id = :category_id AND id <> :product_id
                 ORDER BY id DESC LIMIT 3'
            );
            $relatedStmt->bindValue(':category_id', (int) $product['category_id'], PDO::PARAM_INT);
            $relatedStmt->bindValue(':product_id', $id, PDO::PARAM_INT);
            $relatedStmt->execute();
            $relatedProducts = $relatedStmt->fetchAll();
        } catch (Throwable $exception) {
            error_log($exception->getMessage());
            http_response_code(503);
            require dirname(__DIR__) . '/Views/customer/unavailable.php';
            return;
        }

        require dirname(__DIR__) . '/Views/customer/product-detail.php';
    }

    private function notFound(): void
    {
        http_response_code(404);
        require dirname(__DIR__) . '/Views/customer/not-found.php';
    }
}
