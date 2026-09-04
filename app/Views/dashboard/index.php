<?php use App\Core\Session; use App\Core\CSRF; ?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Dashboard</title><link rel="stylesheet" href="/assets/css/app.css"></head>
<body><header><strong>Multi-Channel Content Manager</strong><form method="post" action="/logout"><input type="hidden" name="_csrf" value="<?=CSRF::token()?>"><button>Logout</button></form></header>
<main class="dashboard"><h1>Welcome, <?=htmlspecialchars((string)Session::get('user_name'))?></h1><div class="grid">
<div class="card"><h3>Installation Complete</h3><p>Database, configuration, admin authentication and modular foundation are active.</p></div>
<div class="card"><h3>Next Phase</h3><p>Google OAuth → encrypted token storage → channel detection.</p></div>
<div class="card"><h3>Architecture</h3><p>Services, repositories, modules and database schema are separated for future features.</p></div>
</div></main></body></html>
