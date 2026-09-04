# Hostinger Installation

1. Create an empty MySQL database/user in Hostinger hPanel.
2. Upload this ZIP into `public_html` and extract it so `index.php` is directly inside `public_html`.
3. Open `https://YOUR-DOMAIN.com/install.php`.
4. Enter the database details, website base URL, Google OAuth Web Client ID/Secret, and admin credentials.
5. The installer creates all database tables, indexes, foreign keys, default settings, encryption key, and Cron secret. You do **not** need to import `database/schema.sql` manually.
6. Log in.
7. Connect Google Drive from **Drive Folders**.
8. Connect YouTube channels from **YouTube Channels**.
9. Assign each Drive folder to its target channel.
10. Configure per-channel metadata and schedules.
11. Configure Hostinger Cron Jobs using the commands/URLs documented in `README.md`.

## Google OAuth redirect URI

The Google OAuth Web Application must contain:

`https://YOUR-DOMAIN.com/oauth/google.php?provider=drive`

and

`https://YOUR-DOMAIN.com/oauth/google.php?provider=youtube`

The exact URL should match the installed domain and HTTPS configuration.
