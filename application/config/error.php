<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Error Detail
	|--------------------------------------------------------------------------
	|
	| Detailed error messages contain information about the file in which an
	| error occurs, as well as a PHP stack trace containing the call stack.
	|
	| If your application is in production, consider turning off error details
	| for enhanced security and user experience. The error stack trace could
	| contain sensitive information that should not be publicly visible.
	|
	*/

	'detail' => true,

	/*
	|--------------------------------------------------------------------------
	| Error Logging
	|--------------------------------------------------------------------------
	|
	| When error logging is enabled, the "logger" Closure defined below will
	| be called for every error in your application. You are free to log the
	| errors however you want. Enjoy the flexibility.
	|
	*/

	'log' => false,

	/*
	|--------------------------------------------------------------------------
	| Error Handler
	|--------------------------------------------------------------------------
	|
	| Because of the various ways of managing errors, you get complete freedom
	| to manage errors as you desire. Any error that occurs in your application
	| will be sent to this Closure.
	|
	| By default, when error detail is disabled, a generic error page will be
	| rendered by the handler. After this handler is complete, the framework
	| will stop processing the request and "exit" will be called.
	|
	*/

	'handler' => function($exception, $config)
	{
		if ( ! $config['detail'])
		{
			Response::error('500')->send();
		}
	},

	/*
	|--------------------------------------------------------------------------
	| Error Logger
	|--------------------------------------------------------------------------
	|
	| Because of the various ways of managing error logging, you get complete
	| flexibility to manage error logging as you see fit. This function will
	| be called anytime an error occurs within your application and error
	| logging is enabled. 
	|
	| You may log the error message however you like; however, a simple log
	| solution has been setup for you which will log all error messages to
	| a single text file within the application storage directory.
	|
	| Of course, you are free to implement more complex solutions including
	| e-mailing the exceptions details to your team, etc.
	|
	*/

	'logger' => function($exception, $config)
	{
		$message = date('Y-m-d H:i:s').' - '.$exception->getMessage().PHP_EOL;

		File::append(STORAGE_PATH.'log.txt', $message);
	}

);