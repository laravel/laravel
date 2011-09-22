<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Error Detail
	|--------------------------------------------------------------------------
	|
	| Detailed error messages contain information about the file in which
	| an error occurs, a stack trace, and a snapshot of the source code
	| in which the error occured.
	|
	| If your application is in production, consider turning off error details
	| for enhanced security and user experience.
	|
	*/

	'detail' => true,

	/*
	|--------------------------------------------------------------------------
	| Error Logging
	|--------------------------------------------------------------------------
	|
	| Error Logging will use the "logger" function defined below to log error
	| messages, which gives you complete freedom to determine how error
	| messages are logged. Enjoy the flexibility.
	|
	*/

	'log' => false,

	/*
	|--------------------------------------------------------------------------
	| Error Handler
	|--------------------------------------------------------------------------
	|
	| Because of the various ways of managing error logging, you get complete
	| flexibility in Laravel to manage error logging as you see fit.
	|
	| This function will be called when an error occurs in your application.
	| You can log the error however you like.
	|
	| The error "severity" passed to the method is a human-readable severity
	| level such as "Parsing Error" or "Fatal Error".
	|
	| A simple logging system has been setup for you. By default, all errors
	| will be logged to the storage/log.txt file.
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

		if ($config['log'])
		{
			call_user_func($config['logger'], $severity, $message);
		}

		$response->send();

		exit(1);
	},

	/*
	|--------------------------------------------------------------------------
	| Error Logger
	|--------------------------------------------------------------------------
	|
	| Because of the various ways of managing error logging, you get complete
	| flexibility to manage error logging as you see fit.
	|
	| This function will be called when an error occurs in your application.
	| You can log the error however you like.
	|
	| The error "severity" passed to the method is a human-readable severity
	| level such as "Parsing Error" or "Fatal Error".
	|
	| A simple logging system has been setup for you. By default, all errors
	| will be logged to the storage/log.txt file.
	|
	*/

	'logger' => function($severity, $message)
	{
		File::append(STORAGE_PATH.'log.txt', date('Y-m-d H:i:s').' '.$severity.' - '.$message.PHP_EOL);
	}

);