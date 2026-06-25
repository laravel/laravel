# Contributing

Thanks for helping improve Larapi Core.

## Local Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

## Quality Checks

Run the same checks used by CI:

```bash
composer ci
```

Useful focused commands:

```bash
composer lint
composer lint:check
composer test
composer test:unit
composer test:feature
```

## Pull Request Guidelines

- Keep changes focused.
- Add or update tests for behavior changes.
- Update README or CHANGELOG entries when public behavior changes.
- Prefer API-first defaults: JSON responses, stateless auth, explicit validation, and no web/session assumptions.
