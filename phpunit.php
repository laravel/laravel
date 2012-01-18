<?php
/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @version  2.2.0 (Beta 1)
 * @author   Taylor Otwell <taylorotwell@gmail.com>
 * @link     http://laravel.com
 */

// --------------------------------------------------------------
// Override the application path if testing Laravel.
// --------------------------------------------------------------
foreach ($_SERVER['argv'] as $key => $argument)
{
	if ($argument == 'laravel' and $_SERVER['argv'][$key - 1] == '--group')
	{
		define('APP_PATH', realpath('tests/laravel').DIRECTORY_SEPARATOR);
	}
}

// --------------------------------------------------------------
// Set the core Laravel path constants.
// --------------------------------------------------------------
require 'paths.php';

// --------------------------------------------------------------
// Bootstrap the Laravel core.
// --------------------------------------------------------------
require SYS_PATH.'core.php';

// --------------------------------------------------------------
// Start the default bundle.
// --------------------------------------------------------------
Bundle::start(DEFAULT_BUNDLE);