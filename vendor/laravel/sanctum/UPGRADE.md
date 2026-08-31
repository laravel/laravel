# Upgrade Guide

## Upgrading To 4.0 From 3.x

### Minimum PHP Version

PHP 8.2 is now the minimum required version.

### Minimum Laravel Version

Laravel 11.0 is now the minimum required version.

### Migration Changes

Sanctum 4.0 no longer automatically loads migrations from its own migrations directory. Instead, you should run the following command to publish Sanctum's migrations to your application:

```bash
php artisan vendor:publish --tag=sanctum-migrations
```

### Configuration Changes

In your application's `config/sanctum.php` configuration file, you should update the references to the `authenticate_session`, `encrypt_cookies`, and `validate_csrf_token` middleware to the following:

```php
'middleware' => [
    'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
    'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
    'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
],
```
