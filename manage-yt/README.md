# Multi-Channel Content Manager

Phase 1 foundation for a production-oriented PHP 8.2+ / MySQL shared-hosting application.

## Deployment
1. Create a MySQL/MariaDB database.
2. Upload and extract the ZIP.
3. Import `database/schema.sql`.
4. Copy `.env.example` to `.env`.
5. Set database, Google OAuth, application URL, encryption key, and cron secret values.
6. Point the domain/document root at `public/` when supported.
7. Open the application.

No installation wizard is included.

## Development
Composer is used for dependency management. The final deployment archive is intended to include `vendor/`.

## Phase status
Phase 1: architecture, database, configuration, security primitives, UI foundation, and future module contracts.
Phases 2-5: intentionally not implemented in this Phase 1 package.
