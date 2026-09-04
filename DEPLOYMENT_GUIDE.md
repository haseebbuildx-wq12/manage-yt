# Hostinger Deployment

Upload this ZIP, extract it, and configure the website document root to the `public` directory where possible. If your Hostinger plan only exposes `public_html`, move the contents of `public/` there and update paths in `public/index.php` only after keeping `app`, `config`, `database`, `install`, and `storage` outside the public web root.

Then open `/install` and complete the wizard.
