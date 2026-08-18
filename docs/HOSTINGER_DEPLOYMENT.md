# Hostinger hPanel deployment

1. Create a MySQL database/user and select PHP 8.2+ with `bcmath`, `fileinfo`, `gd`, `mbstring`, `openssl`, `pdo_mysql`, and `zip`.
2. Deploy outside `public_html` and point the domain document root at the project's `public` directory. Application source, `.env`, and private storage must not be web-accessible.

   If your Hostinger shared plan does not allow changing the document root, upload the whole project into `public_html` including the repository-root `.htaccess`. It internally serves `public/`, removes `/public` from URLs, disables directory listings, and blocks direct access to Laravel source and secret files.
3. Run `composer install --no-dev --optimize-autoloader`, ensure the project root and `storage/app` are writable, then visit `https://DOMAIN/install.php`.
4. Follow Requirements → Database → Admin → Install. The wizard creates `.env`, tests MySQL, runs migrations, creates the first company administrator, and writes `storage/app/installed.json` so it cannot run again.
5. Build locally with `npm ci && npm run build`, then deploy `public/build`.
6. Add an every-minute cron: `/usr/bin/php /home/USER/domains/DOMAIN/PROJECT/artisan schedule:run`. It drains bounded database queue batches.
7. Force HTTPS, restrict `.env` permissions, and optionally remove `public/install.php` after successful installation as defense in depth.
8. Schedule daily MySQL/private-storage backups and test restoration quarterly in a separate database.

## Release

Run maintenance mode, install optimized production dependencies, migrate, optimize caches, smoke-test `/up`, then leave maintenance mode.

## Updating an existing installation

Upload the new application files, keep the existing `.env`, and run `composer install --no-dev --optimize-autoloader`, `php artisan migrate --force`, and `php artisan optimize` from the project directory. The browser installer is intentionally locked after the first installation and does not perform upgrades. Back up MySQL and `storage/app/private` before every update.
