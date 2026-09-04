# YT Multi-Channel Automation — Production Hostinger Build

PHP 8.1+ / MySQL / Google OAuth 2.0 / Google Drive API / YouTube Data API v3. Designed for Hostinger Business shared hosting. No VPS, Docker, Node.js, Python worker, Redis or Supervisor.

## What this build implements

- Database-driven unlimited channel/folder mappings.
- Separate schedule, timezone and metadata defaults per YouTube channel.
- Multiple Google Drive folders mapped to channels.
- Drive File ID is unique, preventing duplicate imports/uploads.
- Google OAuth 2.0 with offline refresh tokens; YouTube passwords are never requested.
- Encrypted OAuth token storage using AES-256-GCM.
- Filename-to-title cleanup with per-video overrides.
- Channel defaults copied into each queued video, so each queued item remains independent.
- Placeholder expansion: `{TITLE}`, `{CHANNEL_NAME}`, `{DATE}`.
- Multiple weekly schedule slots and timezone-aware conversion to UTC.
- Manual per-video schedule with its own timezone.
- Collision prevention for channel schedule slots.
- Separate Cron workers for scan, queue/upload, retry, token refresh and maintenance.
- MySQL Cron locks to prevent duplicate worker execution.
- Resumable YouTube uploads in 8 MB chunks by default.
- Drive video is downloaded only to a temporary file for the upload attempt and deleted afterward.
- Configurable temporary file size limit.
- Automatic retry of transient failures with exponential backoff and retry cap.
- Permanent failures stay Failed until manual Retry.
- One folder/channel failure is logged without aborting unrelated mappings.
- Dashboard, channels, Drive mappings, queue, schedules, history, failed uploads, settings and logs.
- Search/filter queue and YouTube links after upload.

## Important scheduling behavior

For `private` videos, the worker uploads the video before the requested publish time and sends YouTube `status.publishAt`. This is required because YouTube scheduled publishing is represented by a private video with a future publish time. The default lead is 10 minutes and is configurable.

For `public` and `unlisted`, the worker starts at the scheduled time because YouTube cannot use `publishAt` to turn those privacy states into a future publication.

All database timestamps are UTC. Schedule rows are interpreted in their configured timezone.

## Installation

1. Upload the project outside the public document root if Hostinger allows it, and point the domain document root to `public/`. If you must place the project under `public_html`, keep `config/`, `app/`, `cron/`, `database/` and `storage/` under the project root and keep the included deny rules intact; only `public/` should be web-accessible. Do not remove the `cron/.htaccess` protection.
2. Create MySQL database/user in hPanel and import `database/schema.sql`.
3. Copy `config/config.example.php` to `config/config.php`.
4. Fill database credentials, `base_url`, Google client ID/secret, `APP_KEY` and `cron_secret`.
5. Generate `APP_KEY`: `php -r "echo bin2hex(random_bytes(32)),PHP_EOL;"`
6. Keep `config/` outside the public web root when Hostinger permits. The included rules also deny direct access to config/storage under Apache.
7. Visit `/install.php` once, create the admin, then remove/rename `install.php`.
8. In Google Cloud create an OAuth 2.0 Web application client. Authorized redirect URI: `https://YOUR-DOMAIN.com/oauth/google.php?provider=youtube` and `https://YOUR-DOMAIN.com/oauth/google.php?provider=drive`.
9. Enable YouTube Data API v3 and Google Drive API.
10. Log in, connect Drive, connect each YouTube channel, map folders, configure channel defaults and schedules.

## Hostinger Cron

Recommended every 5 minutes:

- `php /home/USERNAME/domains/YOUR-DOMAIN/public_html/cron/scan_drive.php`
- `php /home/USERNAME/domains/YOUR-DOMAIN/public_html/cron/process_queue.php`
- `php /home/USERNAME/domains/YOUR-DOMAIN/public_html/cron/retry_failed.php`

Every 15 minutes:

- `php /home/USERNAME/domains/YOUR-DOMAIN/public_html/cron/refresh_tokens.php`

Daily:

- `php /home/USERNAME/domains/YOUR-DOMAIN/public_html/cron/maintenance.php`

If Hostinger only offers URL Cron, use:
`https://YOUR-DOMAIN.com/cron/run.php?job=scan&key=YOUR_CRON_SECRET`
with `queue`, `retry`, `refresh`, and `maintenance` for the other jobs.

## Drive folder mapping

The dashboard asks for a folder ID, validates it against Drive, and stores the folder name. This avoids silently saving an invalid ID. The scanner handles Drive pagination and supported common video MIME types.

## Security

Passwords use `password_hash()`. SQL uses PDO prepared statements. POST forms use CSRF tokens. OAuth state is validated. Tokens are encrypted at rest. Refresh tokens are never shown. Temporary uploads are removed after each attempt and old temporary files are cleaned by maintenance.

## Shared-hosting limitation

A shared-hosting Cron job still has PHP execution/time and storage limits. The upload is resumable at the YouTube protocol level, but this implementation intentionally completes a resumable upload within one worker execution rather than pretending a PHP process can remain alive forever. Configure Hostinger's PHP/cron limits appropriately and use a reasonable video size. For very large files beyond the shared-hosting execution window, a VPS/worker would be required by the hosting environment; the application itself does not require one for normal-sized files.
