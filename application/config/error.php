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
	| Because of the various ways of managing error logging, you get complete
	| flexibility in Laravel to manage all error logging as you see fit.
	|
	| This function will be called when an error occurs in your application.
	| You are free to handle the exception any way you want. The severity
	| will be a human-readable severity level such as "Parsing Error".
	|
	*/

	'handler' => function($exception, $severity, $message, $config)
	{
		if ($config['detail'])
		{
			$data = compact('exception', 'severity', 'message');

			$response = Response::view('error.exception', $data)->status(500);
		}
		else
		{
			$response = Response::error('500');
		}

		$response->send();
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
	| You may log the error message however you like; however, a simple logging
	| solution has been setup for you which will log all error messages to a
	| single text file within the application storage directory.
	|
	*/

	'logger' => function($exception, $severity, $message, $config)
	{
		$message = date('Y-m-d H:i:s').' '.$severity.' - '.$message.PHP_EOL;

		File::append(STORAGE_PATH.'log.txt', $message);
	}

);