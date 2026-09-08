<h1 style="margin-top:0;font-size:20px;">Sign in</h1>
<?php if (!empty($error)): ?>
    <p style="color:#f87171;"><?= e($error) ?></p>
<?php endif; ?>
<form method="post" action="/login">
    <input type="hidden" name="_csrf" value="<?= e(\App\Core\Csrf::token()) ?>">
    <div style="margin-bottom:12px;">
        <label>Email</label><br>
        <input type="email" name="email" required style="width:100%;padding:8px;border-radius:8px;border:1px solid #252e47;background:#0d1324;color:#eef2ff;">
    </div>
    <div style="margin-bottom:16px;">
        <label>Password</label><br>
        <input type="password" name="password" required style="width:100%;padding:8px;border-radius:8px;border:1px solid #252e47;background:#0d1324;color:#eef2ff;">
    </div>
    <button type="submit" class="btn" style="width:100%;">Login</button>
</form>