<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Middleware\AuthMiddleware;

final class DashboardController extends Controller {
    public function index(): void {
        AuthMiddleware::check();
        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'user' => ['name' => $_SESSION['user_name'] ?? ''],
        ], 'app');
    }
}