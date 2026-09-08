<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="/assets/css/app.css">
<link rel="stylesheet" href="/assets/css/components/buttons.css">
<link rel="stylesheet" href="/assets/css/components/cards.css">
<link rel="stylesheet" href="/assets/css/components/forms.css">
<link rel="stylesheet" href="/assets/css/components/modals.css">
<link rel="stylesheet" href="/assets/css/components/tables.css">
<link rel="stylesheet" href="/assets/css/components/toasts.css">
<link rel="stylesheet" href="/assets/css/utilities/helpers.css">
<meta charset="UTF-8">
<title><?= e($title ?? 'Dashboard') ?></title>
<link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <h2 style="font-size:18px;">Multi-Channel CM</h2>
        <nav style="margin-top:20px;">
    <p style="color:var(--muted);font-size:12px;text-transform:uppercase;">Menu</p>
    <div style="display:flex;flex-direction:column;gap:8px;margin:10px 0 20px;">
        <a href="/dashboard">Dashboard</a>
        <a href="/channels">Channels</a>
        <a href="/videos">Videos</a>
        <a href="/drive">Google Drive</a>
        <a href="/analytics">Analytics</a>
        <a href="/research">Research</a>
    </div>
    <p style="color:var(--muted);font-size:12px;text-transform:uppercase;">System</p>
    <div style="display:flex;flex-direction:column;gap:8px;margin:10px 0 20px;">
        <a href="/settings">Settings</a>
    </div>
    <form method="post" action="/logout">
        <input type="hidden" name="_csrf" value="<?= e(\App\Core\Csrf::token()) ?>">
        <button type="submit" class="btn">Logout</button>
    </form>
</nav>
    </aside>
    <div class="main">
        <div class="topbar"><strong><?= e($title ?? 'Dashboard') ?></strong></div>
        <div class="content"><?= $content ?></div>
    </div>
</div>
</body>
</html>