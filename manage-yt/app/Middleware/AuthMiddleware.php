<?php
declare(strict_types=1);
namespace App\Middleware;

final class AuthMiddleware {
    public static function check(): void {
        if (empty($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }
}