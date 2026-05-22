<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

require_once __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Ensure Test Environment Variables
|--------------------------------------------------------------------------
|
| PHPUnit sets environment variables from phpunit.xml <php> section, but
| these can be overridden by variables already present in the process
| environment (e.g., from running `php artisan serve` or `composer run dev`
| in the same terminal). Dotenv's immutable repository will not overwrite
| existing process-level variables, so .env.testing values get ignored.
|
| This bootstrap ensures critical test variables are forced into all layers
| (putenv, $_ENV, $_SERVER) before the application bootstraps.
|
*/

$variables = [
    'APP_ENV' => 'testing',
    'BCRYPT_ROUNDS' => '4',
    'CACHE_STORE' => 'array',
    'DB_CONNECTION' => $_SERVER['DB_CONNECTION'] ?? 'sqlite',
    'DB_DATABASE' => $_SERVER['DB_DATABASE'] ?? ':memory:',
    'MAIL_MAILER' => 'array',
    'QUEUE_CONNECTION' => 'sync',
    'SESSION_DRIVER' => 'array',
];

foreach ($variables as $key => $value) {
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
    putenv("{$key}={$value}");
}
