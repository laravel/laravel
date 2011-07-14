<?php

// --------------------------------------------------------------
// Define the framework paths.
// --------------------------------------------------------------
define('BASE_PATH', realpath('../').'/');
define('APP_PATH', realpath('../application').'/');
define('SYS_PATH', realpath('../system').'/');
define('PUBLIC_PATH', realpath('../public').'/');
define('PACKAGE_PATH', APP_PATH.'packages/');

// --------------------------------------------------------------
// Define the PHP file extension.
// --------------------------------------------------------------
define('EXT', '.php');

// --------------------------------------------------------------
// Load the classes used by the auto-loader.
// --------------------------------------------------------------
require SYS_PATH.'config'.EXT;
require SYS_PATH.'arr'.EXT;

// --------------------------------------------------------------
// Load the test utilities.
// --------------------------------------------------------------
require 'utils'.EXT;

// --------------------------------------------------------------
// Register the auto-loader.
// --------------------------------------------------------------
spl_autoload_register(require SYS_PATH.'loader'.EXT);