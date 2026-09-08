<div class="flex-between mb-16">
    <h2 style="margin:0;">Channels</h2>
    <?php if (!empty($googleAccounts)): ?>
        <a href="/channels/available" class="btn">+ Connect New Channel</a>
    <?php else: ?>
        <a href="/google/connect" class="btn">Connect Google Account</a>
    <?php endif; ?>
</div>

<?php if (!empty($error)): ?>
    <p style="color:#f87171;">Google connection error: <?= e($error) ?></p>
<?php endif; ?>

<?php if (!empty($googleAccounts)): ?>
<div class="card mb-16">
    <div class="card-header"><h3 class="card-title">Google Accounts</h3></div>
    <?php foreach ($googleAccounts as $acc): ?>
        <div class="flex-between" style="padding:8px 0;border-bottom:1px solid #252e47;">
            <div>
                <?= e($acc['email']) ?>
                <span class="badge <?= $acc['status'] === 'connected' ? 'badge-success' : 'badge-danger' ?>"><?= e($acc['status']) ?></span>
            </div>
            <?php if ($acc['status'] !== 'connected'): ?>
                <a href="/google/connect" class="btn btn-sm">Reconnect</a>
            <?php else: ?>
                <form method="post" action="/google/disconnect">
                    <input type="hidden" name="_csrf" value="<?= e(\App\Core\Csrf::token()) ?>">
                    <input type="hidden" name="google_account_id" value="<?= e((string)$acc['id']) ?>">
                    <button type="submit" class="btn btn-sm btn-secondary">Disconnect</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (empty($channels)): ?>
    <div class="card empty-state">
        <h3>Koi channel connect nahi hai</h3>
        <p>Google account connect karein aur apne YouTube channels select karein.</p>
    </div>
<?php else: ?>
    <div class="grid">
        <?php foreach ($channels as $ch): ?>
            <div class="card">
                <h3 style="margin:0 0 6px;"><?= e($ch['title']) ?></h3>
                <p class="text-muted" style="margin:0;"><?= e($ch['handle'] ?? $ch['custom_url'] ?? '') ?></p>
                <p class="text-muted" style="margin:8px 0 0;font-size:13px;"><?= number_format((int) $ch['subscriber_count']) ?> subscribers</p>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>