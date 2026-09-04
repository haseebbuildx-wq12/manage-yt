# Multi-Channel Content Manager

## Phase 1 package
1. Upload/extract project.
2. Point domain/document root to `public/`.
3. Ensure `storage/` and `config/` are writable during installation.
4. Open `/install`.
5. Run requirements check, database installation, app setup, Google credentials, and admin creation.

The installer creates the complete database schema now, including future Google, channel, Drive, video, scheduling, analytics, cron, module and import tables.
