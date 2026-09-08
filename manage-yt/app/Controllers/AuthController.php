<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Models\User;

final class AuthController extends Controller {
    public function showLogin(): void {
        if (!empty($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }
        $this->view('auth/login', ['title' => 'Login'], 'guest');
    }

    public function login(): void {
        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            $this->view('auth/login', ['title' => 'Login', 'error' => 'Session expired, try again.'], 'guest');
            return;
        }
        $email = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        $user = User::findByEmail($email);

        if (!$user || !$user->verifyPassword($password) || $user->status !== 'active') {
            $this->view('auth/login', ['title' => 'Login', 'error' => 'Invalid email or password.'], 'guest');
            return;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_role'] = $user->role;

        header('Location: /dashboard');
        exit;
    }

    public function forgotPassword(): void {
        $this->view('auth/forgot-password', ['title' => 'Forgot Password'], 'guest');
    }

    public function logout(): void {
        Csrf::verify($_POST['_csrf'] ?? null);
        $_SESSION = [];
        session_destroy();
        header('Location: /login');
        exit;
    }
}