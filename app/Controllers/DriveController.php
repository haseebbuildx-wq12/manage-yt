<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Middleware\AuthMiddleware;

final class DriveController extends Controller {
    public function index(): void {
        AuthMiddleware::check();
        $this->view('drive/index', ['title' => 'Drives'], 'app');
    }
}