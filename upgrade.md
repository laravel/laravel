# Laravel Upgrade Guide

## Upgrading From 4.0 to 4.1

- Update `composer.json` to require `"laravel/framework": "4.1.*"`
- `composer update`.
- Replace `public/index.php`, `artisan.php`.
- Add new `app/config/remote.php` file.
- Add new `expire_on_close` and `secure` options to `session` configuration file.
- Add new `failed` queue job option to `queue` configuration file.
- Remove call to `redirectIfTrailingSlash` in `bootstrap/start.php` file.
- Edit `app/config/database.php` and update `redis.cluster` option to `false` to turn Redis clustering off by default.
- Edit `app/config/view.php` and update `pagination` option to use bootstrap 3 as default pagination view.
- Edit `app/config/app.php`; 
  - in `aliases` change `'Controller' => 'Illuminate\Routing\Controllers\Controller',`
  to use `Illuminate\Routing\Controller`.
  - in `providers` add `'Illuminate\Remote\RemoteServiceProvider',`.
  - in `aliases` add `'SSH' => 'Illuminate\Support\Facades\SSH',`.
- Edit `app/controllers/BaseController.php` change `use Illuminate\Routing\Controllers\Controller;` to `use Illuminate\Routing\Controller;`.
- If you are overriding `missingMethod` in your controllers, add $method as the first parameter.
- Password reminder system tweaked for greater developer freedom. Inspect stub controller by running `auth:reminders-controller` Artisan command.
- Update `reminders.php` language file.
- If you are using http hosts to set the $env variable in bootstrap/start.php, these should be changed to machine names (as returned by PHP's gethostname() function).
