<?php

error_reporting(E_ALL | E_STRICT);

// Set the default timezone. While this doesn't cause any tests to fail, PHP
// complains if it is not set in 'date.timezone' of php.ini.
date_default_timezone_set('UTC');

// Ensure that composer has installed all dependencies
if (!file_exists(dirname(__DIR__) . '/composer.lock')) {
    die("Dependencies must be installed using composer:\n\nphp composer.phar install --dev\n\n"
        . "See http://getcomposer.org for help with installing composer\n");
}

// Include the composer autoloader
$autoloader = require_once dirname(__DIR__) . '/vendor/autoload.php';

$client = \Elasticsearch\ClientBuilder::fromConfig([
    'hosts' => [$_SERVER['ES_TEST_HOST']]
]);

$count = 0;
while (!$client->ping()) {
    $count += 1;

    if ($count > 15) {
        throw new \Exception("Live cluster could not be found in 15s!");
    }
    sleep(1);
}

