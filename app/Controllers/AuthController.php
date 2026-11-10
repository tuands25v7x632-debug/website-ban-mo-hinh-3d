<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Database;
use App\Support\Auth;
use App\Support\Cart;
use PDOException;
use Throwable;

final class AuthController
{
    public function registerForm(): void
    {
        if (Auth::userId() !== null) {
            header('Location: /', true, 303);
            return;
        }
        $title = 'Đăng ký tài khoản';
        $action = '/register';
        $submitLabel = 'Tạo tài khoản';
        $isRegistration = true;
        $errors = [];
        $old = ['name' => '', 'email' => ''];
        require dirname(__DIR__) . '/Views/customer/auth-form.php';
    }

    public function register(): void
    {
        $this->requireCsrf();
        if (Auth::userId() !== null) {
            header('Location: /', true, 303);
            return;
        }
        $name = trim(is_string($_POST['name'] ?? null) ? $_POST['name'] : '');
        $email = strtolower(trim(is_string($_POST['email'] ?? null) ? $_POST['email'] : ''));
        $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
        $confirm = is_string($_POST['confirm_password'] ?? null) ? $_POST['confirm_password'] : '';
        $errors = [];

        if ($name === '' || mb_strlen($name) > 120) {
            $errors[] = 'Họ tên phải có từ 1 đến 120 ký tự.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
            $errors[] = 'Email không hợp lệ.';
        }
        if (strlen($password) < 8 || strlen($password) > 72) {
            $errors[] = 'Mật khẩu phải dài từ 8 đến 72 byte.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Mật khẩu nhập lại không khớp.';
        }
        if (!$errors) {
            try {
                $pdo = Database::connect();
                $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :hash, :role)');
                $stmt->execute([
                    'name' => $name,
                    'email' => $email,
                    'hash' => password_hash($password, PASSWORD_DEFAULT),
                    'role' => 'customer',
                ]);
                Auth::login(['id' => (int) $pdo->lastInsertId(), 'name' => $name, 'role' => 'customer']);
                header('Location: /', true, 303);
                return;
            } catch (PDOException $exception) {
                if ($exception->getCode() === '23000') {
                    $errors[] = 'Email này đã được sử dụng.';
                } else {
                    error_log($exception->getMessage());
                    $errors[] = 'Không thể tạo tài khoản. Vui lòng thử lại.';
                }
            } catch (Throwable $exception) {
                error_log($exception->getMessage());
                $errors[] = 'Không thể tạo tài khoản. Vui lòng thử lại.';
            }
        }
        http_response_code(422);
        $title = 'Đăng ký tài khoản';
        $action = '/register';
        $submitLabel = 'Tạo tài khoản';
        $isRegistration = true;
        $old = ['name' => $name, 'email' => $email];
        require dirname(__DIR__) . '/Views/customer/auth-form.php';
    }

    public function loginForm(): void
    {
        if (Auth::userId() !== null) {
            header('Location: /', true, 303);
            return;
        }
        $title = 'Đăng nhập';
        $action = '/login';
        $submitLabel = 'Đăng nhập';
        $isRegistration = false;
        $errors = [];
        $old = ['email' => ''];
        require dirname(__DIR__) . '/Views/customer/auth-form.php';
    }

    public function login(): void
    {
        $this->requireCsrf();
        if (Auth::userId() !== null) {
            header('Location: /', true, 303);
            return;
        }
        $email = strtolower(trim(is_string($_POST['email'] ?? null) ? $_POST['email'] : ''));
        $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
        $errors = [];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190 || $password === '') {
            $errors[] = 'Email hoặc mật khẩu không chính xác.';
        } else {
            try {
                $pdo = Database::connect();
                $stmt = $pdo->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = :email LIMIT 1');
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch();
                if ($user && password_verify($password, (string) $user['password_hash'])) {
                    Auth::login($user);
                    header('Location: /', true, 303);
                    return;
                }
                $errors[] = 'Email hoặc mật khẩu không chính xác.';
            } catch (Throwable $exception) {
                error_log($exception->getMessage());
                $errors[] = 'Không thể đăng nhập lúc này. Vui lòng thử lại.';
            }
        }
        http_response_code(422);
        $title = 'Đăng nhập';
        $action = '/login';
        $submitLabel = 'Đăng nhập';
        $isRegistration = false;
        $old = ['email' => $email];
        require dirname(__DIR__) . '/Views/customer/auth-form.php';
    }

    public function logout(): void
    {
        $this->requireCsrf();
        Auth::logout();
        header('Location: /', true, 303);
    }

    private function requireCsrf(): void
    {
        if (!Cart::validCsrf(is_string($_POST['csrf_token'] ?? null) ? $_POST['csrf_token'] : null)) {
            http_response_code(403);
            exit('403 - CSRF token không hợp lệ');
        }
    }
}
