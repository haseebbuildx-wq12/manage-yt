<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Middleware\AuthMiddleware;

final class ChannelController extends Controller {
    public function index(): void {
        AuthMiddleware::check();
        $this->view('channels/index', ['title' => 'Channels'], 'app');
    }
}