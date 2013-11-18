# Laravel Upgrade Guide

## Upgrading From 4.0 to 4.1

- `composer update`.
- Replace `public/index.php`, `artisan.php`.
- Add new `expire_on_close` option to `session` configuration file.
- Remove call to `redirectIfTrailingSlash` in `bootstrap/start.php` file.
- Edit `app/config/app.php`; in `aliases` change `'Controller' => 'Illuminate\Routing\Controllers\Controller',`
  to use `Illuminate\Routing\Controller` and add `'SSH'             => 'Illuminate\Support\Facades\SSH',`
- Edit `app/controllers/BaseController.php` change `use Illuminate\Routing\Controllers\Controller;` to `use Illuminate\Routing\Controller;
`
- If you are overriding `missingMethod` in your controllers, add $method as the first parameter.
- Password reminder system tweaked for greater developer freedom. Inspect stub controller by running `auth:reminders-controller` Artisan command.
- Update `reminders.php` language file.
