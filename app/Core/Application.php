<?php
declare(strict_types=1);

namespace App\Core;

use App\Controllers\AuthController;
use App\Controllers\DashboardController;

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

        $router->get('/', function () {
            header('Location: ' . (!empty($_SESSION['user_id']) ? '/dashboard' : '/login'));
            exit;
        });
        $router->get('/login', [AuthController::class, 'showLogin']);
        $router->post('/login', [AuthController::class, 'login']);
        $router->post('/logout', [AuthController::class, 'logout']);
        $router->get('/dashboard', [DashboardController::class, 'index']);
        $router->get('/channels', [\App\Controllers\ChannelController::class, 'index']);
        $router->get('/videos', [\App\Controllers\VideoController::class, 'index']);
        $router->get('/drive', [\App\Controllers\DriveController::class, 'index']);
        $router->get('/analytics', [\App\Controllers\AnalyticsController::class, 'index']);
        $router->get('/research', [\App\Controllers\ResearchController::class, 'index']);
        $router->get('/settings', [\App\Controllers\SettingsController::class, 'index']);
        $router->get('/forgot-password', function () {
    (new \App\Controllers\AuthController())->view('auth/forgot-password', ['title' => 'Forgot Password'], 'guest');
});

        $response = $router->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
        if (is_string($response)) echo htmlspecialchars($response, ENT_QUOTES, 'UTF-8');
    }
}