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