# Laravel Upgrade Guide

## Upgrading From 4.0 to 4.1

- `composer update`.
- Replace `public/index.php`, `artisan.php`.
- Add new `expire_on_close` option to `session` configuration file.
- Remove call to `redirectIfTrailingSlash` in `bootstrap/start.php` file.
- Edit `app/config/app.php`; in `aliases` change `'Controller' => 'Illuminate\Routing\Controllers\Controller',`
  to use `Illuminate\Routing\Controller`
