<h2 style="margin-top:0;">Settings</h2>

<?php if (!empty($saved)): ?>
    <div class="toast success mb-16" style="position:static;">Settings saved.</div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="toast error mb-16" style="position:static;"><?= e($error) ?></div>
<?php endif; ?>

<form method="post" action="/settings">
    <input type="hidden" name="_csrf" value="<?= e(\App\Core\Csrf::token()) ?>">

    <div class="card mb-16">
        <div class="card-header"><h3 class="card-title">General</h3></div>
        <div class="form-group">
            <label>Application Name</label>
            <input class="form-control" name="app_name" value="<?= e($settings['app_name']) ?>">
        </div>
        <div class="form-group">
            <label>Application URL</label>
            <input class="form-control" name="app_url" value="<?= e($settings['app_url']) ?>" placeholder="https://example.com">
        </div>
        <div class="form-group">
            <label>Timezone</label>
            <input class="form-control" name="app_timezone" value="<?= e($settings['app_timezone']) ?>" placeholder="UTC">
            <p class="form-hint">TODO: replace with a timezone dropdown once locale support lands.</p>
        </div>
    </div>

    <div class="card mb-16">
        <div class="card-header"><h3 class="card-title">Branding</h3></div>
        <div class="form-group">
            <label>Logo Path / URL</label>
            <input class="form-control" name="logo_path" value="<?= e($settings['logo_path']) ?>" placeholder="/assets/images/logo.png">
            <p class="form-hint">TODO: replace with a real upload field in the phase that implements media handling.</p>
        </div>
        <div class="form-group">
            <label>Favicon Path / URL</label>
            <input class="form-control" name="favicon_path" value="<?= e($settings['favicon_path']) ?>" placeholder="/assets/images/favicon.ico">
        </div>
        <div class="grid">
            <div class="form-group">
                <label>Primary Color</label>
                <input class="form-control" type="color" name="color_primary" value="<?= e($settings['color_primary']) ?>">
            </div>
            <div class="form-group">
                <label>Secondary Color</label>
                <input class="form-control" type="color" name="color_secondary" value="<?= e($settings['color_secondary']) ?>">
            </div>
            <div class="form-group">
                <label>Accent Color</label>
                <input class="form-control" type="color" name="color_accent" value="<?= e($settings['color_accent']) ?>">
            </div>
        </div>
    </div>

    <div class="card mb-16">
        <div class="card-header"><h3 class="card-title">Google Drive</h3></div>
        <div class="form-group">
            <label>Google Drive Root Folder ID</label>
            <input class="form-control" name="drive_root_folder_id" value="<?= e($settings['drive_root_folder_id']) ?>">
        </div>
    </div>

    <div class="card mb-16">
        <div class="card-header"><h3 class="card-title">Default Upload Settings</h3></div>
        <div class="grid">
            <div class="form-group">
                <label>Default Privacy</label>
                <select class="form-control" name="default_upload_privacy">
                    <?php foreach (['private', 'unlisted', 'public'] as $opt): ?>
                        <option value="<?= e($opt) ?>" <?= $settings['default_upload_privacy'] === $opt ? 'selected' : '' ?>><?= e(ucfirst($opt)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Default Category</label>
                <input class="form-control" name="default_upload_category" value="<?= e($settings['default_upload_category']) ?>">
            </div>
            <div class="form-group">
                <label>Default Tags (comma separated)</label>
                <input class="form-control" name="default_upload_tags" value="<?= e($settings['default_upload_tags']) ?>">
            </div>
        </div>
    </div>

    <button type="submit" class="btn">Save Settings</button>
</form>

<div class="card mt-16">
    <div class="card-header"><h3 class="card-title">Google &amp; Cron (.env managed)</h3></div>
    <p class="form-hint">These are secrets. They are stored only in <code>.env</code> on the server and are never written to the database or fully displayed here.</p>
    <table class="table">
        <tbody>
            <tr><td>Google Client ID</td><td><?= e($secrets['google_client_id']) ?></td></tr>
            <tr><td>Google Client Secret</td><td><?= e($secrets['google_client_secret']) ?></td></tr>
            <tr><td>Google Redirect URI</td><td><?= e($secrets['google_redirect_uri']) ?></td></tr>
            <tr><td>Cron Secret</td><td><?= e($secrets['cron_secret']) ?></td></tr>
        </tbody>
    </table>
</div>
