<?php
namespace App\Controllers;

use App\Core\CSRF;
use App\Core\Session;
use App\Core\Database;

final class AuthController {
    private function db(): \PDO { $c=require BASE_PATH.'/config/installed.php'; return Database::connect($c['db']); }

    public function showLogin(): void {
        if (!is_file(BASE_PATH.'/storage/installed.lock')) { header('Location: /install'); exit; }
        require BASE_PATH.'/app/Views/auth/login.php';
    }
    public function login(): void {
        if (!CSRF::validate()) { http_response_code(419); exit('Invalid request'); }
        $stmt=$this->db()->prepare("SELECT * FROM users WHERE email=? AND status='active' LIMIT 1");
        $stmt->execute([trim($_POST['email']??'')]); $u=$stmt->fetch();
        if (!$u || !password_verify((string)($_POST['password']??''),$u['password_hash'])) {
            Session::put('login_error','Invalid email or password.'); header('Location: /login'); exit;
        }
        Session::regenerate(); Session::put('user_id',(int)$u['id']); Session::put('user_name',$u['name']); header('Location: /');
    }
    public function logout(): void { Session::forget('user_id'); Session::forget('user_name'); Session::regenerate(); header('Location: /login'); }
}
