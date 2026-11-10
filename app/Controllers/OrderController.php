<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Database;
use App\Support\Auth;
use App\Support\Cart;
use PDO;
use Throwable;
use RuntimeException;

final class OrderController
{
    public function checkout(): void
    {
        Auth::requireCustomer();
        $cart = Cart::all();
        if (!$cart) {
            Cart::message('warning', 'Giỏ hàng đang trống.');
            header('Location: /cart', true, 303);
            return;
        }
        try {
            $pdo = Database::connect();
            $items = $this->loadItems($pdo, $cart, false);
            $total = $this->total($items);
        } catch (Throwable $exception) {
            error_log($exception->getMessage());
            http_response_code(503);
            require dirname(__DIR__) . '/Views/customer/unavailable.php';
            return;
        }
        $notice = Cart::consumeMessage();
        require dirname(__DIR__) . '/Views/customer/checkout.php';
    }

    public function place(): void
    {
        Auth::requireCustomer();
        if (!Cart::validCsrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
            http_response_code(403);
            exit('403 - CSRF token không hợp lệ');
        }
        $cart = Cart::all();
        if (!$cart) {
            Cart::message('warning', 'Giỏ hàng đang trống.');
            header('Location: /cart', true, 303);
            return;
        }
        $pdo = null;
        try {
            $pdo = Database::connect();
            $pdo->beginTransaction();

            // SELECT FOR UPDATE khóa các dòng sản phẩm khi kiểm tra tồn kho.
            $items = $this->loadItems($pdo, $cart, true);
            $total = $this->total($items);
            $userId = Auth::userId();
            if ($userId === null) {
                throw new RuntimeException('Phiên đăng nhập không hợp lệ.');
            }
            $ownerStmt = $pdo->prepare("SELECT id FROM users WHERE id = :id AND role = 'customer' LIMIT 1");
            $ownerStmt->execute(['id' => $userId]);
            if (!$ownerStmt->fetchColumn()) {
                throw new RuntimeException('Tài khoản không có quyền đặt hàng.');
            }

            $orderStmt = $pdo->prepare(
                "INSERT INTO orders (user_id, total, status) VALUES (:uid, :total, 'pending')"
            );
            $orderStmt->execute(['uid' => $userId, 'total' => $total]);
            $orderId = (int) $pdo->lastInsertId();

            $itemStmt = $pdo->prepare(
                'INSERT INTO order_items (order_id, product_id, quantity, unit_price)
                 VALUES (:oid, :pid, :qty, :price)'
            );
            $stockStmt = $pdo->prepare(
                'UPDATE products SET stock = stock - :qty_decrease WHERE id = :pid AND stock >= :qty_check'
            );
            foreach ($items as $item) {
                $quantity = (int) $item['quantity'];
                $itemStmt->execute([
                    'oid' => $orderId,
                    'pid' => (int) $item['id'],
                    'qty' => $quantity,
                    'price' => $item['price'],
                ]);
                $stockStmt->execute(['qty_decrease' => $quantity, 'pid' => (int) $item['id'], 'qty_check' => $quantity]);
                if ($stockStmt->rowCount() !== 1) {
                    throw new RuntimeException('Không đủ tồn kho: ' . $item['name']);
                }
            }
            $pdo->commit();
            $_SESSION['cart'] = [];
            Cart::message('success', 'Đặt hàng thành công! Mã đơn hàng #' . $orderId);
            header('Location: /orders/' . $orderId, true, 303);
            return;
        } catch (\PDOException $exception) {
            if ($pdo instanceof PDO && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log($exception->getMessage());
            Cart::message('danger', 'Không thể xử lý đơn hàng. Vui lòng thử lại.');
            header('Location: /cart', true, 303);
            return;
        } catch (RuntimeException $exception) {
            if ($pdo instanceof PDO && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            Cart::message('warning', $exception->getMessage());
            header('Location: /cart', true, 303);
            return;
        } catch (Throwable $exception) {
            if ($pdo instanceof PDO && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log($exception->getMessage());
            Cart::message('danger', 'Không thể đặt hàng. Vui lòng thử lại.');
            header('Location: /cart', true, 303);
            return;
        }
    }

    public function index(): void
    {
        Auth::requireCustomer();
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare(
                'SELECT id, total, status, created_at FROM orders
                 WHERE user_id = :uid ORDER BY id DESC'
            );
            $stmt->execute(['uid' => Auth::userId()]);
            $orders = $stmt->fetchAll();
        } catch (Throwable $exception) {
            error_log($exception->getMessage());
            http_response_code(503);
            require dirname(__DIR__) . '/Views/customer/unavailable.php';
            return;
        }
        $notice = Cart::consumeMessage();
        require dirname(__DIR__) . '/Views/customer/orders.php';
    }

    public function show(string $rawId): void
    {
        Auth::requireCustomer();
        if (!ctype_digit($rawId) || $rawId === '0' || strlen($rawId) > 10
            || ($id = filter_var($rawId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) === false) {
            $this->notFound();
            return;
        }
        try {
            $pdo = Database::connect();
            // IDOR: ràng buộc user_id để khách không thể xem đơn hàng của người khác.
            $stmt = $pdo->prepare(
                'SELECT id, total, status, created_at FROM orders WHERE id = :id AND user_id = :uid LIMIT 1'
            );
            $stmt->execute(['id' => $id, 'uid' => Auth::userId()]);
            $order = $stmt->fetch();
            if (!$order) {
                $this->notFound();
                return;
            }
            $itemsStmt = $pdo->prepare(
                'SELECT oi.product_id, oi.quantity, oi.unit_price, p.name
                 FROM order_items oi
                 INNER JOIN products p ON p.id = oi.product_id
                 WHERE oi.order_id = :id ORDER BY oi.id'
            );
            $itemsStmt->execute(['id' => $id]);
            $items = $itemsStmt->fetchAll();
        } catch (Throwable $exception) {
            error_log($exception->getMessage());
            http_response_code(503);
            require dirname(__DIR__) . '/Views/customer/unavailable.php';
            return;
        }
        $notice = Cart::consumeMessage();
        require dirname(__DIR__) . '/Views/customer/order-detail.php';
    }

    private function loadItems(PDO $pdo, array $cart, bool $forUpdate): array
    {
        $ids = array_keys($cart);
        sort($ids, SORT_NUMERIC);
        $marks = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT id, name, price, stock FROM products WHERE id IN ($marks) ORDER BY id";
        if ($forUpdate) {
            $sql .= ' FOR UPDATE';
        }
        $stmt = $pdo->prepare($sql);
        foreach ($ids as $index => $id) {
            $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
        }
        $stmt->execute();
        $found = $stmt->fetchAll();
        if (count($found) !== count($cart)) {
            throw new RuntimeException('Một sản phẩm trong giỏ đã bị xóa.');
        }
        foreach ($found as &$product) {
            $quantity = $cart[(int) $product['id']];
            if ($quantity < 1 || $quantity > 99 || $quantity > (int) $product['stock']) {
                throw new RuntimeException('Số lượng vượt tồn kho: ' . $product['name']);
            }
            $product['quantity'] = $quantity;
            $product['subtotal'] = round((float) $product['price'] * $quantity, 2);
        }
        unset($product);
        return $found;
    }

    private function total(array $items): string
    {
        // Dùng phép tính số nguyên theo đơn vị nhỏ nhất để giữ chính xác DECIMAL(12,2).
        $sumCents = 0;
        foreach ($items as $item) {
            $price = (string) $item['price'];
            if (!preg_match('/^(\d+)(?:\.(\d{1,2}))?$/', $price, $parts)) {
                throw new RuntimeException('Giá sản phẩm không hợp lệ.');
            }
            $cents = (int) $parts[1] * 100
                + (int) str_pad($parts[2] ?? '', 2, '0');
            $sumCents += $cents * (int) $item['quantity'];
        }
        return intdiv($sumCents, 100) . '.' . str_pad((string) ($sumCents % 100), 2, '0', STR_PAD_LEFT);
    }

    private function notFound(): void
    {
        http_response_code(404);
        require dirname(__DIR__) . '/Views/customer/not-found.php';
    }
}
