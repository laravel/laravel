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
 * Define a closure that will return a more readable version
 * of the severity of an exception. This function will be used
 * by the error handler when parsing exceptions.
 */
$severity = function($e)
{
	if ($e instanceof \ErrorException)
	{
		switch ($e->getSeverity())
		{
			default:
			case E_ERROR:
			case E_RECOVERABLE_ERROR:
				return 'Error';
			case E_WARNING:
				return 'Warning';
			case E_PARSE:
				return 'Parsing Error';
			case E_NOTICE
				return 'Notice';
			case E_CORE_ERROR:
				return 'Core Error';
			case E_CORE_WARNING:
				return 'Core Warning';
			case E_COMPILE_ERROR:
				return 'Compile Error';
			case E_COMPILE_WARNING:
				return 'Compile Warning';
			case E_USER_ERROR:
				return 'User Error';
			case E_USER_WARNING:
				return 'User Warning';
			case E_USER_NOTICE:
				return 'User Notice';
			case E_STRICT:
				return 'Runtime Notice';
			case E_DEPRECATED:
				return 'Deprecated';
			case E_USER_DEPRECATED:
				return 'User Deprecated';
		}
	}
	return 'Uncaught Exception';
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
	// Ignore errors from @func(...) calls
	if (error_reporting() === 0)
	{
		return false;
	}
	
	// Check the severity to see whether this error should be reported
	if ( ! (error_reporting() & $number))
	{
		return true;
	}
	
	$handler(new \ErrorException($error, 0, $number, $file, $line));
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

		$handler(new \ErrorException($message, 0, $type, $file, $line));
	}
});

/**
 * Turn off all PHP error reporting and display. Since the framework
 * will be displaying the exception messages, we don't want PHP to
 * display any ugly error information.
 */
ini_set('display_errors', 'Off');

/**
 * Set the error reporting level from the user's configuration.
 * -1 is used to report absolutely every error, warning and notice.
 */
error_reporting(Config::get('error.level', -1));
