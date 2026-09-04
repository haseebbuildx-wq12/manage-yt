<?php use App\Core\CSRF; use App\Core\Session; ?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login</title><link rel="stylesheet" href="/assets/css/app.css"></head>
<body class="center"><main class="card"><h1>Multi-Channel Content Manager</h1><p>Sign in to continue</p>
<?php if ($e=Session::get('login_error')): \App\Core\Session::forget('login_error'); ?><div class="error"><?=htmlspecialchars($e)?></div><?php endif; ?>
<form method="post" action="/login"><input type="hidden" name="_csrf" value="<?=CSRF::token()?>"><label>Email</label><input name="email" type="email" required><label>Password</label><input name="password" type="password" required><button>Login</button></form>
<p class="muted">Google login and password reset modules are scaffolded for Phase 2.</p></main></body></html>
