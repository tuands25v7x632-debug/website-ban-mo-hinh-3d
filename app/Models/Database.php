<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class Database
{
    public static function connect(): PDO
    {
        $file = dirname(__DIR__, 2) . '/config/database.php';
        if (!is_file($file)) {
            throw new \RuntimeException('Chưa tạo config/database.php');
        }
        $config = require $file;
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $config['host'],
            (int) $config['port'],
            $config['database']
        );
        return new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
}
