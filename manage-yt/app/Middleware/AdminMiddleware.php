<?php
declare(strict_types=1);
namespace App\Middleware;

final class AdminMiddleware {
    /**
     * Ensures the current session belongs to an authenticated admin user.
     * Redirects to the dashboard (with 403) for any other role.
     */
    public static function check(): void {
        AuthMiddleware::check();
        if (($_SESSION['user_role'] ?? null) !== 'admin') {
            http_response_code(403);
            header('Location: /dashboard');
            exit;
        }
    }
}
