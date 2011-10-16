<?php namespace Laravel;

/**
 * Create the exception formatter closure. This function will format
 * the exception message and severity for display and return the two
 * formatted strings in an array.
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
 * Create the exception handler function. All of the error handlers
 * registered with PHP call this closure to keep the code D.R.Y.
 */
$handler = function($e) use ($formatter)
{
	list($severity, $message) = $formatter($e);

	$config = Config::get('error');

	if ($config['log'])
	{
		call_user_func($config['logger'], $e, $severity, $message, $config);
	}

	call_user_func($config['handler'], $e, $severity, $message, $config);

	exit(1);
};

/**
 * Register the exception, error, and shutdown error handlers.
 * These handlers will catch all PHP exceptions and errors and
 * pass the exceptions into the common Laravel error handler.
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
		extract($error, EXTR_SKIP);

		$handler(new \ErrorException($message, $type, 0, $file, $line));
	}	
});

/**
 * Set the error reporting and display levels. Since the framework
 * will be displaying the exception messages, we don't want PHP to
 * display any error information.
 */
error_reporting(-1);

ini_set('display_errors', 'Off');