<?php
declare(strict_types=1);
namespace App\Middleware;

use App\Core\Csrf;

final class CsrfMiddleware {
    /**
     * Verifies the CSRF token on state-changing (POST/PUT/PATCH/DELETE) requests.
     * Controllers can call this at the top of write actions instead of
     * repeating Csrf::verify() calls inline.
     */
    public static function verify(): void {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return;
        }
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            echo 'Session expired, please refresh and try again.';
            exit;
        }
    }
}
