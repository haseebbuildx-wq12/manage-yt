<div class="card"><h2>System configuration</h2><p><b>Application timezone:</b> <?=e($config['app']['timezone'])?></p><p><b>Base URL:</b> <?=e($config['app']['base_url'])?></p><p><b>Upload chunk:</b> <?=e((string)($config['upload']['chunk_size']/1024/1024))?> MB</p><p><b>Private-video upload lead:</b> <?=e((string)$config['upload']['private_upload_lead_minutes'])?> minutes</p><p>Secrets are stored outside the dashboard and are never rendered.</p></div><div class="card"><h2>Cron</h2><p>Prefer CLI Cron if Hostinger provides it. Otherwise use the protected URL endpoint.</p><pre>php /home/USERNAME/domains/YOUR-DOMAIN/public_html/cron/scan_drive.php
php /home/USERNAME/domains/YOUR-DOMAIN/public_html/cron/process_queue.php
php /home/USERNAME/domains/YOUR-DOMAIN/public_html/cron/retry_failed.php
php /home/USERNAME/domains/YOUR-DOMAIN/public_html/cron/refresh_tokens.php
php /home/USERNAME/domains/YOUR-DOMAIN/public_html/cron/maintenance.php</pre></div>
