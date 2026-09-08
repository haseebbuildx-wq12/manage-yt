<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Middleware\AuthMiddleware;

final class VideoController extends Controller {
    public function index(): void {
        AuthMiddleware::check();
        $this->view('videos/index', ['title' => 'Videos'], 'app');
    }
}