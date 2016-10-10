<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (file_exists(__DIR__.'/../autoload.php')) {
    require __DIR__.'/../autoload.php';
} elseif (@include('Predis/Autoloader.php')) {
    Predis\Autoloader::register();
} else {
    die('ERROR: Unable to find a suitable mean to register Predis\Autoloader.');
}

require __DIR__.'/PHPUnit/ArrayHasSameValuesConstraint.php';
require __DIR__.'/PHPUnit/RedisCommandConstraint.php';
require __DIR__.'/PHPUnit/PredisTestCase.php';
require __DIR__.'/PHPUnit/PredisCommandTestCase.php';
require __DIR__.'/PHPUnit/PredisConnectionTestCase.php';
require __DIR__.'/PHPUnit/PredisProfileTestCase.php';
require __DIR__.'/PHPUnit/PredisDistributorTestCase.php';
