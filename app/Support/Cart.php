<?php
declare(strict_types=1);

namespace App\Support;

/**
 * Session chỉ lưu product_id => quantity, KHÔNG lưu giá gửi từ trình duyệt.
 */
final class Cart
{
    public static function all(): array
    {
        $raw = $_SESSION['cart'] ?? [];
        if (!is_array($raw)) {
            return [];
        }
        $clean = [];
        foreach ($raw as $id => $quantity) {
            if (!ctype_digit((string) $id) || (int) $id < 1 || (int) $id > 2147483647) {
                continue;
            }
            if (!is_int($quantity) || $quantity < 1 || $quantity > 999) {
                continue;
            }
            $clean[(int) $id] = $quantity;
        }
        $_SESSION['cart'] = $clean;
        return $clean;
    }

    public static function count(): int
    {
        return array_sum(self::all());
    }

    public static function set(int $id, int $quantity): void
    {
        $cart = self::all();
        if ($quantity === 0) {
            unset($cart[$id]);
        } else {
            $cart[$id] = $quantity;
        }
        $_SESSION['cart'] = $cart;
    }

    public static function message(string $type, string $message): void
    {
        $_SESSION['cart_flash'] = ['type' => $type, 'message' => $message];
    }

    public static function consumeMessage(): ?array
    {
        $message = $_SESSION['cart_flash'] ?? null;
        unset($_SESSION['cart_flash']);
        return is_array($message) ? $message : null;
    }

    public static function csrfToken(): string
    {
        if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validCsrf(?string $token): bool
    {
        return is_string($token)
            && $token !== ''
            && hash_equals(self::csrfToken(), $token);
    }
}
