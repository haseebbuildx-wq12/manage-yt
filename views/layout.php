<?php $title = ucfirst($page); ?>
<!doctype html>
<html><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($title)?> — YT Automation</title>
<link rel="stylesheet" href="<?=e(base_url('assets/app.css'))?>">
</head><body>
<div class="shell">
<aside class="sidebar">
  <div class="brand">YT Automation</div>
  <nav>
    <?php foreach(['dashboard'=>'Dashboard','channels'=>'YouTube Channels','folders'=>'Drive Folders','videos'=>'Video Queue','schedules'=>'Upload Schedule','history'=>'Upload History','failed'=>'Failed Uploads','settings'=>'Settings','logs'=>'Logs'] as $k=>$v): ?>
      <a class="<?= $page===$k?'active':'' ?>" href="<?=e(base_url('index.php?page='.$k))?>"><?=e($v)?></a>
    <?php endforeach; ?>
  </nav>
  <a class="logout" href="<?=e(base_url('logout.php'))?>">Logout</a>
</aside>
<main class="main">
<header><h1><?=e($title)?></h1><div class="muted"><?=date('d M Y, H:i')?></div></header>
<?php if(!empty($_GET['error'])):?><div class="alert danger"><?=e($_GET['error'])?></div><?php endif;?>
<?php require __DIR__.'/'.$page.'.php'; ?>
</main>
</div>
</body></html>
