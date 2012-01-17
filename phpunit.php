<?php

// --------------------------------------------------------------
// Overrride the application path if testing Laravel.
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