<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Middleware\AuthMiddleware;

final class SettingsController extends Controller {
    public function index(): void {
        AuthMiddleware::check();
        $this->view('settings/index', ['title' => 'Settings'], 'app');
    }
}