<?php

/*
|--------------------------------------------------------------------------
| Installation Paths
|--------------------------------------------------------------------------
|
| Here you may specify the location of the various Laravel framework
| directories for your installation. 
|
| Of course, these are already set to the proper default values, so you do
| not need to change them if you have not modified the directory structure.
|
*/

$application = '../application';

$laravel     = '../laravel';

$packages    = '../packages';

$storage     = '../storage';

$public      = '../public';

/*
|--------------------------------------------------------------------------
| Test Path Constants
|--------------------------------------------------------------------------
*/

define('FIXTURE_PATH', realpath('Fixtures').'/');

/*
|--------------------------------------------------------------------------
| Bootstrap The Laravel Core
|--------------------------------------------------------------------------
*/

require realpath($laravel).'/bootstrap/core.php';