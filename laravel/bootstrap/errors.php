<?php namespace Laravel;

/**
 * Create the exception formatter closure. This function will format
 * the exception message and severity for display and return the
 * two formatted strings in an array.
 */
$formatter = function($e)
{
	$levels = array(
		0                  => 'Error',
		E_ERROR            => 'Error',
		E_WARNING          => 'Warning',
		E_PARSE            => 'Parsing Error',
		E_NOTICE           => 'Notice',
		E_CORE_ERROR       => 'Core Error',
		E_CORE_WARNING     => 'Core Warning',
		E_COMPILE_ERROR    => 'Compile Error',
		E_COMPILE_WARNING  => 'Compile Warning',
		E_USER_ERROR       => 'User Error',
		E_USER_WARNING     => 'User Warning',
		E_USER_NOTICE      => 'User Notice',
		E_STRICT           => 'Runtime Notice',
	);

	$file = str_replace(array(APP_PATH, SYS_PATH), array('APP_PATH/', 'SYS_PATH/'), $e->getFile());

	$message = rtrim($e->getMessage(), '.').' in '.$file.' on line '.$e->getLine().'.';

	$severity = (array_key_exists($e->getCode(), $levels)) ? $levels[$e->getCode()] : $e->getCode();

	return array($severity, $message);
};

/**
 * Create the exception handler function. All of the handlers registered
 * with PHP will call this handler when an error occurs, giving us a common
 * spot to put error handling logic.
 */
$handler = function($e) use ($formatter)
{
	list($severity, $message) = $formatter($e);

	call_user_func(Config::get('error.handler'), $e, $severity, $message, Config::get('error'));
};

/**
 * Register the exception, error, and shutdown error handlers.
 */
set_exception_handler(function($e) use ($handler)
{
	$handler($e); 
});

set_error_handler(function($number, $error, $file, $line) use ($handler)
{
	$handler(new \ErrorException($error, $number, 0, $file, $line));
});

register_shutdown_function(function() use ($handler)
{
	if ( ! is_null($error = error_get_last()))
	{
		$handler(new \ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']));
	}	
});

/**
 * Set the error reporting and display levels.
 */
error_reporting(-1);

ini_set('display_errors', 'Off');