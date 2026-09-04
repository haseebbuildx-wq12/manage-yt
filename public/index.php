<?php
declare(strict_types=1);

// DEVELOPMENT ONLY — Show PHP errors on screen
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Router;
use App\Controllers\InstallController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;

$router = new Router();

$router->get('/install', [InstallController::class, 'index']);
$router->post('/install/requirements', [InstallController::class, 'requirements']);
$router->post('/install/database/test', [InstallController::class, 'testDatabase']);
$router->post('/install/database', [InstallController::class, 'installDatabase']);
$router->post('/install/application', [InstallController::class, 'application']);
$router->post('/install/google', [InstallController::class, 'google']);
$router->post('/install/admin', [InstallController::class, 'admin']);
$router->post('/install/complete', [InstallController::class, 'complete']);

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/', [DashboardController::class, 'index']);

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);