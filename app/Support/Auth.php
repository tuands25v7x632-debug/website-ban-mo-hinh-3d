<?php
declare(strict_types=1);

namespace App\Support;

final class Auth
{
    public static function userId(): ?int
    {
        $id = $_SESSION['user_id'] ?? null;
        return is_int($id) && $id > 0 ? $id : null;
    }

    public static function name(): string
    {
        return is_string($_SESSION['user_name'] ?? null) ? $_SESSION['user_name'] : '';
    }

    public static function role(): string
    {
        return is_string($_SESSION['user_role'] ?? null) ? $_SESSION['user_role'] : '';
    }

    public static function isCustomer(): bool
    {
        return self::userId() !== null && self::role() === 'customer';
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_name'] = (string) $user['name'];
        $_SESSION['user_role'] = (string) $user['role'];
    }

    public static function logout(): void
    {
        unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_role']);
        session_regenerate_id(true);
        unset($_SESSION['csrf_token']);
    }

    public static function requireCustomer(): void
    {
        if (self::userId() === null) {
            header('Location: /login', true, 303);
            exit;
        }
        if (!self::isCustomer()) {
            http_response_code(403);
            exit('403 - Chỉ khách hàng mới được đặt hàng.');
        }
    }
}
