<?php
declare(strict_types=1);

namespace App\Core;

use PDO;

final class Database {
    private static ?PDO $pdo = null;

    public static function init(): void {
        if (self::$pdo) return;
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4',
            Env::get('DB_HOST', '127.0.0.1'),
            Env::get('DB_DATABASE', '')
        );
        self::$pdo = new PDO($dsn, Env::get('DB_USERNAME', ''), Env::get('DB_PASSWORD', ''), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
    public static function pdo(): PDO {
        if (!self::$pdo) self::init();
        return self::$pdo;
    }
}
