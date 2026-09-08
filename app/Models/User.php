<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Database;

final class User {
    public function __construct(public array $attributes = []) {}

    public function __get(string $name): mixed {
        return $this->attributes[$name] ?? null;
    }

    public static function findByEmail(string $email): ?self {
        $stmt = Database::pdo()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ? new self($row) : null;
    }

    public function verifyPassword(string $password): bool {
        return password_verify($password, $this->attributes['password_hash'] ?? '');
    }
}