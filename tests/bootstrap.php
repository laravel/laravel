<?php

require __DIR__.'/functions.php';

/*
|--------------------------------------------------------------------------
| Installation Paths
|--------------------------------------------------------------------------
*/

$application = __DIR__.'/../application';

$laravel     = __DIR__.'/../laravel';

$public      = __DIR__.'/../public';

define('FIXTURE_PATH', __DIR__.'/fixtures/');

/*
|--------------------------------------------------------------------------
| Bootstrap The Laravel Core
|--------------------------------------------------------------------------
*/

require realpath($laravel).'/core.php';