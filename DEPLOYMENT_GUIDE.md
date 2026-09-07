# Hostinger Deployment Guide

## Requirements
- PHP 8.2+
- MySQL/MariaDB
- PHP PDO + OpenSSL + cURL + JSON extensions
- Apache/.htaccess support

## Steps
1. Create the database in Hostinger hPanel.
2. Upload the project ZIP and extract it.
3. Import `database/schema.sql` using phpMyAdmin.
4. Copy `.env.example` to `.env` and enter production values.
5. Prefer configuring the domain/subdomain document root to `public/`. If the hosting plan cannot change the document root, consult the hosting configuration before exposing the application.
6. Ensure `storage/logs`, `storage/cache`, and `storage/temp` are writable by PHP.
7. Never expose `.env`, storage, or source directories publicly.
8. Set `APP_DEBUG=false` in production.

## Google Cloud
Enable:
- YouTube Data API v3
- YouTube Analytics API
- Google Drive API

Create an OAuth consent screen and a **Web application** OAuth client. Do not use desktop/installed-app flow.

Set the authorized redirect URI to the exact value used by `GOOGLE_REDIRECT_URI`.

## Cron
Phase 1 only provides the cron foundation. The actual upload scheduler is implemented in Phase 3.
