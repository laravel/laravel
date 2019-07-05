<?php

use Illuminate\Contracts\Console\Kernel;

require_once __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Bootstrap the testing environment
|--------------------------------------------------------------------------
|
| You have the option to specify console commands that will execute before your
| test suite is run. Caching config, routes, & events may improve performance
| and bring your testing environment closer to production.
|
*/

$commands = [
    'config:cache',
    'event:cache',
    // 'route:cache',
];

$app = require __DIR__.'/../bootstrap/app.php';

$console = tap($app->make(Kernel::class))->bootstrap();

foreach ($commands as $command) {
    $console->call($command);
}

