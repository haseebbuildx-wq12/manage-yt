<h2 style="margin-top:0;">Connect Channels</h2>

<?php if (!empty($error)): ?>
    <p style="color:#f87171;"><?= e($error) ?></p>
<?php endif; ?>

<?php if (empty($available)): ?>
    <div class="card empty-state">
        <h3>Koi naya channel nahi mila</h3>
        <p>Ya sab channels pehle se connected hain, ya is Google account par koi YouTube channel nahi.</p>
    </div>
<?php else: ?>
<form method="post" action="/channels/connect">
    <input type="hidden" name="_csrf" value="<?= e(\App\Core\Csrf::token()) ?>">
    <div class="card mb-16">
        <?php foreach ($available as $ch): ?>
            <label style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #252e47;">
                <input type="checkbox" name="channels[]" value="<?= e($ch['google_account_id'] . '|' . $ch['id'] . '|' . $ch['title']) ?>">
                <span><?= e($ch['title']) ?></span>
                <span class="text-muted" style="font-size:12px;"><?= $ch['subscriberCount'] !== null ? number_format($ch['subscriberCount']) . ' subs' : '' ?></span>
            </label>
        <?php endforeach; ?>
    </div>
    <button type="submit" class="btn">Connect Selected Channels</button>
</form>
<?php endif; ?>