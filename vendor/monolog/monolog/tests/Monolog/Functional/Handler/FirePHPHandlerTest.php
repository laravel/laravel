<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

spl_autoload_register(function ($class) {
    $file = __DIR__.'/../../../../src/'.strtr($class, '\\', '/').'.php';
    if (file_exists($file)) {
        require $file;

        return true;
    }
});

use Monolog\Logger;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\ChromePHPHandler;

$logger = new Logger('firephp');
$logger->pushHandler(new FirePHPHandler);
$logger->pushHandler(new ChromePHPHandler());

$logger->addDebug('Debug');
$logger->addInfo('Info');
$logger->addWarning('Warning');
$logger->addError('Error');
