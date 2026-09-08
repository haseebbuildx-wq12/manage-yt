<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Middleware\AuthMiddleware;

final class ResearchController extends Controller {
    public function index(): void {
        AuthMiddleware::check();
        $this->view('research/index', ['title' => 'Research'], 'app');
    }
}