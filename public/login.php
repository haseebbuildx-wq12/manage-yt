<?php
require_once __DIR__.'/../app/bootstrap.php';
if ($security->adminId()) redirect(base_url('index.php'));
$error = null;
if (is_post()) {
    $security->verifyCsrf(post('_csrf'));
    $admin = $db->one("SELECT * FROM admins WHERE email=?", [trim((string)post('email'))]);
    if ($admin && password_verify((string)post('password'), $admin['password_hash'])) {
        $security->login((int)$admin['id']); redirect(base_url('index.php'));
    }
    $error = 'Invalid email or password.';
}
?><!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login</title><link rel="stylesheet" href="<?=e(base_url('assets/app.css'))?>"></head><body class="auth"><div class="auth-card"><h1>YT Automation</h1><?php if($error):?><div class="alert danger"><?=e($error)?></div><?php endif;?><form method="post"><input type="hidden" name="_csrf" value="<?=e($security->csrf())?>"><label>Email</label><input name="email" type="email" required><label>Password</label><input name="password" type="password" required><button class="btn primary">Login</button></form></div></body></html>
