<?php
declare(strict_types=1);

namespace App\Support;

use App\Models\Database;
use Throwable;

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
        unset($_SESSION['csrf_token']);
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
        $id = self::userId();
        if ($id === null) {
            header('Location: /login', true, 303);
            exit;
        }
        // Quyền phải được kiểm tra ở CSDL, không chỉ dựa vào vai trò cũ trong Session.
        try {
            $stmt = Database::connect()->prepare('SELECT name, role FROM users WHERE id = :id LIMIT 1');
            $stmt->execute(['id' => $id]);
            $user = $stmt->fetch();
        } catch (Throwable $exception) {
            error_log($exception->getMessage());
            http_response_code(503);
            exit('Dịch vụ tạm thời không khả dụng.');
        }
        if (!$user) {
            self::logout();
            header('Location: /login', true, 303);
            exit;
        }
        $_SESSION['user_name'] = (string) $user['name'];
        $_SESSION['user_role'] = (string) $user['role'];
        if ($user['role'] !== 'customer') {
            http_response_code(403);
            exit('403 - Chỉ khách hàng mới có thể xem hoặc đặt đơn hàng.');
        }
    }

}
