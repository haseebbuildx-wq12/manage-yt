# Hostinger Setup

This project targets PHP shared hosting. Node.js, Python, Docker, Redis, and a long-running worker are not required.

- Upload ZIP and extract.
- Import `database/schema.sql`.
- Configure `.env`.
- Point the web root to `public/` where the plan permits.
- Keep secrets and storage outside public exposure.
- Phase 3 will document the exact production cron invocation after the cron runner is implemented.
