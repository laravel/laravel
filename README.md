# MealFlow

Multi-company meal delivery operations system built with Laravel 12, Livewire-compatible Blade UI, Sanctum, MySQL, and Tailwind CSS.

## Included

- Tenant-isolated company, building, room, customer, meal plan, delivery, billing, payment, receipt, ledger, collection, device, and cash-handover data.
- Platform and company admin dashboards with responsive master-data screens.
- Versioned Sanctum API with UUIDs, pagination, incremental-sync timestamps, record versions, and idempotency keys.
- Fixed-monthly and delivered-daily invoicing, immutable ledgers, payment conflict checks, sequential PDF receipts, and WhatsApp links.
- Role middleware, account/company disabling, login throttling, passport masking, audit events, private storage, and database-backed scheduled jobs.
- Employee/building assignments, pauses and skips, bulk deliveries, supervised delivery/payment corrections, daily cash closing, device approval, customer ledgers, notifications, and CSV/XLSX/PDF reports.

## Local setup

Requirements: PHP 8.2+, Composer, Node 20+, and SQLite or MySQL.

```bash
composer install
cp .env.example .env
php artisan key:generate
# Configure DB_*; for SQLite create database/database.sqlite
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Demo seed credentials all use `ChangeMe!12345`:

- `platform@example.com` — platform administrator
- `admin@demo.test` — company administrator
- `collector@demo.test` — collection employee

Change or delete these accounts before public deployment.

Run `php artisan test` and `npm run build`. See [API specification](docs/openapi.yaml) and [Hostinger deployment](docs/HOSTINGER_DEPLOYMENT.md).

## Browser installer

On a fresh Hostinger deployment, open `https://your-domain.example/install.php`. The wizard checks PHP requirements, tests MySQL, writes `.env`, runs migrations, creates the first company administrator, and locks itself after completion.

When the complete repository is uploaded directly into Hostinger's `public_html`, also upload the hidden root `.htaccess` file. It serves the Laravel `public` directory internally, so clean URLs such as `/login` and `/buildings` work without a `/public` prefix.
