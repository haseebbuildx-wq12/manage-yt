<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Middleware\AuthMiddleware;
use App\Services\Settings\SettingsService;

final class SettingsController extends Controller {
    public function index(): void {
        AuthMiddleware::check();
        $service = SettingsService::make();
        $this->view('settings/index', [
            'title'    => 'Settings',
            'settings' => $service->all(),
            'secrets'  => $service->envSecretsStatus(),
            'saved'    => isset($_GET['saved']),
        ], 'app');
    }

    public function update(): void {
        AuthMiddleware::check();

        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            $this->view('settings/index', [
                'title'    => 'Settings',
                'settings' => SettingsService::make()->all(),
                'secrets'  => SettingsService::make()->envSecretsStatus(),
                'error'    => 'Session expired, please try again.',
            ], 'app');
            return;
        }

        SettingsService::make()->save($_POST);

        header('Location: /settings?saved=1');
        exit;
    }
}
