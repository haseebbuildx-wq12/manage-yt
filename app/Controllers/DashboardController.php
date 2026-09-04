<?php
namespace App\Controllers;

use App\Core\Session;
final class DashboardController {
    public function index(): void {
        if (!Session::get('user_id')) { header('Location: /login'); exit; }
        require BASE_PATH.'/app/Views/dashboard/index.php';
    }
}
