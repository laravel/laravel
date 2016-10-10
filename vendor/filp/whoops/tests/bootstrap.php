<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 *
 * Bootstraper for PHPUnit tests.
 */
error_reporting(E_ALL | E_STRICT);
$_ENV['whoops-test'] = true;
$loader = require_once __DIR__ . '/../vendor/autoload.php';
$loader->add('Whoops\\', __DIR__);
