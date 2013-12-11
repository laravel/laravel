# Laravel Upgrade Guide

## Upgrading From 4.0 to 4.1

- Update `composer.json` to require `"laravel/framework": "4.1.*"`
- Remove call to `redirectIfTrailingSlash` in `/bootstrap/start.php` file.
- Replace `/public/index.php` with [this](https://github.com/laravel/laravel/blob/develop/public/index.php) file, and `/artisan` with [this](https://github.com/laravel/laravel/blob/develop/artisan) file.
- Add new `app/config/remote.php` file from [here](https://github.com/laravel/laravel/blob/develop/app/config/remote.php)
- Add new `expire_on_close` and `secure` options to `session` configuration file to match [this](https://github.com/laravel/laravel/blob/develop/app/config/session.php) file.
- Add new `failed` queue job option to `queue` configuration file to match [this](https://github.com/laravel/laravel/blob/develop/app/config/queue.php) file.
- Edit `app/config/database.php` and update `redis.cluster` option to `false` to turn Redis clustering off by default.
- Edit `app/config/view.php` and update `pagination` option to use bootstrap 3 as default pagination view (optional).
- Edit `app/config/app.php` so the `aliases` and `providers` array match [this](https://github.com/laravel/laravel/blob/develop/app/config/app.php) file:
  - in `aliases` change `'Controller' => 'Illuminate\Routing\Controllers\Controller',`
  to use `Illuminate\Routing\Controller`.
  - in `providers` add `'Illuminate\Remote\RemoteServiceProvider',`.
  - in `aliases` add `'SSH' => 'Illuminate\Support\Facades\SSH',`.
- If `app/controllers/BaseController.php` has a use statement at the top, change `use Illuminate\Routing\Controllers\Controller;` to `use Illuminate\Routing\Controller;`. You may also remove this use statament, for you have registered a class alias for this.
- If you are overriding `missingMethod` in your controllers, add $method as the first parameter.
- Password reminder system tweaked for greater developer freedom. Inspect stub controller by running `auth:reminders-controller` Artisan command.
- Update `reminders.php` language file to match [this](https://github.com/laravel/laravel/blob/master/app/lang/en/reminders.php) file.
- If you are using http hosts to set the $env variable in bootstrap/start.php, these should be changed to machine names (as returned by PHP's gethostname() function).

Finally,

- Run `composer update`
