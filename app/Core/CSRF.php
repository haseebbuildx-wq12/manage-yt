<?php
namespace App\Core;

final class CSRF {
    public static function token(): string {
        if (!Session::get('_csrf')) Session::put('_csrf', bin2hex(random_bytes(32)));
        return Session::get('_csrf');
    }
    public static function validate(): bool {
        return hash_equals((string)Session::get('_csrf'), (string)($_POST['_csrf'] ?? ''));
    }
}
