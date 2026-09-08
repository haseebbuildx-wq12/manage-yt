<h2 style="margin-top:0;">Settings</h2>
<div class="card mb-16">
    <div class="card-header"><h3 class="card-title">General</h3></div>
    <div class="form-group">
        <label>Application Name</label>
        <input class="form-control" value="<?= e(getenv('APP_NAME') ?: 'Multi-Channel Content Manager') ?>" disabled>
    </div>
</div>
<div class="card empty-state">
    <h3>Google, Cron, aur Security settings</h3>
    <p>Ye sections respective phases (2 aur 3) activate hone ke baad yahan aayenge.</p>
</div>