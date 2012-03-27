<?php

// --------------------------------------------------------------
// Define the directory separator for the environment.
// --------------------------------------------------------------
define('DS', DIRECTORY_SEPARATOR);

// --------------------------------------------------------------
// Set the core Laravel path constants.
// --------------------------------------------------------------
require 'paths.php';

// --------------------------------------------------------------
// Override the application paths when testing the core.
// --------------------------------------------------------------
$path = path('base').'tests'.DS;

set_path('app', $path.'application'.DS);

set_path('bundle', $path.'bundles'.DS);

set_path('storage', $path.'storage'.DS);

// --------------------------------------------------------------
// Bootstrap the Laravel core.
// --------------------------------------------------------------
require path('sys').'core.php';

// --------------------------------------------------------------
// Start the default bundle.
// --------------------------------------------------------------
Laravel\Bundle::start(DEFAULT_BUNDLE);