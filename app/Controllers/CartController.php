<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Database;
use App\Support\Cart;
use PDO;
use Throwable;

final class CartController
{
    private const MAX_QUANTITY = 99;

    public function index(): void
    {
        $cart = Cart::all();
        $items = [];
        $total = 0.0;
        $error = false;
        $notice = Cart::consumeMessage();

        try {
            if ($cart !== []) {
                $pdo = Database::connect();
                $ids = array_keys($cart);
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $stmt = $pdo->prepare(
                    "SELECT id, name, price, stock, image FROM products WHERE id IN ($placeholders)"
                );
                foreach ($ids as $index => $id) {
                    $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
                }
                $stmt->execute();

                $found = [];
                foreach ($stmt->fetchAll() as $product) {
                    $id = (int) $product['id'];
                    $quantity = (int) $cart[$id];
                    $subtotal = (float) $product['price'] * $quantity;
                    $product['quantity'] = $quantity;
                    $product['subtotal'] = $subtotal;
                    $product['exceeds_stock'] = $quantity > (int) $product['stock'];
                    $items[] = $product;
                    $total += $subtotal;
                    $found[$id] = true;
                }
                // Không giữ mã sản phẩm đã bị xóa khỏi CSDL.
                foreach ($ids as $id) {
                    if (!isset($found[$id])) {
                        Cart::set((int) $id, 0);
                    }
                }
            }
        } catch (Throwable $exception) {
            error_log($exception->getMessage());
            $error = true;
            http_response_code(503);
        }

        require dirname(__DIR__) . '/Views/customer/cart.php';
    }

    public function add(): void
    {
        $this->requirePostAndCsrf();
        $id = $this->positiveInteger($_POST['product_id'] ?? null);
        $quantity = $this->positiveInteger($_POST['quantity'] ?? null);
        if ($id === null || $quantity === null || $quantity > self::MAX_QUANTITY) {
            Cart::message('danger', 'Mã sản phẩm hoặc số lượng không hợp lệ.');
            $this->redirect('/cart');
        }

        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare('SELECT stock FROM products WHERE id = :id');
            $stmt->execute(['id' => $id]);
            $product = $stmt->fetch();
            if (!$product) {
                Cart::message('danger', 'Sản phẩm không tồn tại.');
            } else {
                $stock = (int) $product['stock'];
                $current = Cart::all()[$id] ?? 0;
                $newQuantity = $current + $quantity;
                if ($stock <= 0 || $newQuantity > $stock || $newQuantity > self::MAX_QUANTITY) {
                    Cart::message('warning', 'Số lượng vượt quá tồn kho hoặc giới hạn giỏ hàng.');
                } else {
                    Cart::set($id, $newQuantity);
                    Cart::message('success', 'Đã thêm sản phẩm vào giỏ hàng.');
                }
            }
        } catch (Throwable $exception) {
            error_log($exception->getMessage());
            Cart::message('danger', 'Không thể kết nối cơ sở dữ liệu. Vui lòng thử lại.');
        }

        $this->redirect('/cart');
    }

    public function update(): void
    {
        $this->requirePostAndCsrf();
        $id = $this->positiveInteger($_POST['product_id'] ?? null);
        $quantity = $this->nonNegativeInteger($_POST['quantity'] ?? null);
        if ($id === null || $quantity === null || $quantity > self::MAX_QUANTITY) {
            Cart::message('danger', 'Thông tin cập nhật không hợp lệ.');
            $this->redirect('/cart');
        }
        if (!array_key_exists($id, Cart::all())) {
            Cart::message('warning', 'Sản phẩm không có trong giỏ hàng.');
            $this->redirect('/cart');
        }
        if ($quantity === 0) {
            Cart::set($id, 0);
            Cart::message('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
            $this->redirect('/cart');
        }

        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare('SELECT stock FROM products WHERE id = :id');
            $stmt->execute(['id' => $id]);
            $product = $stmt->fetch();
            if (!$product || (int) $product['stock'] < $quantity) {
                Cart::message('warning', 'Số lượng vượt quá số lượng còn trong kho.');
            } else {
                Cart::set($id, $quantity);
                Cart::message('success', 'Đã cập nhật số lượng.');
            }
        } catch (Throwable $exception) {
            error_log($exception->getMessage());
            Cart::message('danger', 'Không thể kết nối cơ sở dữ liệu. Vui lòng thử lại.');
        }
        $this->redirect('/cart');
    }

    public function remove(): void
    {
        $this->requirePostAndCsrf();
        $id = $this->positiveInteger($_POST['product_id'] ?? null);
        if ($id === null) {
            Cart::message('danger', 'Mã sản phẩm không hợp lệ.');
        } else {
            Cart::set($id, 0);
            Cart::message('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
        }
        $this->redirect('/cart');
    }

    private function requirePostAndCsrf(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            exit('405 - Method Not Allowed');
        }
        if (!Cart::validCsrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
            http_response_code(403);
            exit('403 - CSRF token không hợp lệ');
        }
    }

    private function positiveInteger(mixed $raw): ?int
    {
        $value = filter_var($raw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        return $value === false || $value === null ? null : $value;
    }

    private function nonNegativeInteger(mixed $raw): ?int
    {
        $value = filter_var($raw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
        return $value === false || $value === null ? null : $value;
    }

    private function redirect(string $location): never
    {
        header('Location: ' . $location, true, 303);
        exit;
    }
}
