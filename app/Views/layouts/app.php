<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= e($title ?? 'Dashboard') ?></title>
<link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <h2 style="font-size:18px;">Multi-Channel CM</h2>
        <nav style="margin-top:20px;">
            <p style="color:var(--muted);font-size:12px;text-transform:uppercase;">Channels</p>
            <p style="color:var(--muted);">No channels connected yet</p>
            <form method="post" action="/logout" style="margin-top:24px;">
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