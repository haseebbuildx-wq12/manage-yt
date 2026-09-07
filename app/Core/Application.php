<?php
declare(strict_types=1);

namespace App\Core;

final class Application {
    public function run(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_set_cookie_params([
                'httponly' => true,
                'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
                'samesite' => 'Lax',
            ]);
            session_start();
        }
        $router = new Router();
        $router->get('/', fn() => 'Multi-Channel Content Manager');
        $response = $router->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
        if (is_string($response)) echo htmlspecialchars($response, ENT_QUOTES, 'UTF-8');
    }
}
