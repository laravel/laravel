<?php namespace Laravel;

/**
 * Define a closure that will return a formatted error message
 * when given an exception. This function will be used by the
 * error handler to create a more readable message.
 */
$message = function($e)
{
	$search = array(APP_PATH, SYS_PATH);

	$replace = array('APP_PATH/', 'SYS_PATH/');

	$file = str_replace($search, $replace, $e->getFile());
	
	return rtrim($e->getMessage(), '.').' in '.$file.' on line '.$e->getLine().'.';	
};

/**
 * Define a closure that will return a more readable version of
 * the severity of an exception. This function will be used by
 * the error handler when parsing exceptions.
 */
$severity = function($e)
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

	if (array_key_exists($e->getCode(), $levels))
	{
		$level = $levels[$e->getCode()];
	}
	else
	{
		$level = $e->getCode();
	}

	return $level;
};

/**
 * Create the exception handler function. All of the error handlers
 * registered by the framework call this closure to avoid duplicate
 * code. Each of the formatting closures defined above will be
 * passed into the handler for convenient use.
 */
$handler = function($e) use ($message, $severity)
{
	$config = Config::get('error');

	if ($config['log'])
	{
		call_user_func($config['logger'], $e, $severity($e), $message($e), $config);
	}

	call_user_func($config['handler'], $e, $severity($e), $message($e), $config);

	exit(1);
};

/**
 * Register the PHP exception handler. The framework throws exceptions
 * on every error that cannot be handled. All of those exceptions will
 * fall into this closure for processing.
 */
set_exception_handler(function($e) use ($handler)
{
	$handler($e); 
});

/**
 * Register the PHP error handler. All PHP errors will fall into this
 * handler, which will convert the error into an ErrorException object
 * and pass the exception into the common exception handler.
 */
set_error_handler(function($number, $error, $file, $line) use ($handler)
{
	$handler(new \ErrorException($error, $number, 0, $file, $line));
});

/**
 * Register the PHP shutdown handler. This function will be called at
 * the end of the PHP script or on a fatal PHP error. If an error has
 * occurred, we will convert it to an ErrorException and pass it to
 * the common exception handler.
 */
register_shutdown_function(function() use ($handler)
{
	if ( ! is_null($error = error_get_last()))
	{
		extract($error, EXTR_SKIP);

		$handler(new \ErrorException($message, $type, 0, $file, $line));
	}	
});

/**
 * Turn off all PHP error reporting and display. Since the framework
 * will be displaying the exception messages, we don't want PHP to
 * display any ugly error information.
 */
error_reporting(-1);

ini_set('display_errors', 'Off');