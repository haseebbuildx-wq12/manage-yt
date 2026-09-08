<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= e($title ?? 'Login') ?></title>
<link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;">
    <div class="card" style="width:360px;">
        <?= $content ?>
    </div>
</div>
</body>
</html>