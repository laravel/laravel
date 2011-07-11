<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Error Detail
	|--------------------------------------------------------------------------
	|
	| Would you like detailed error messages?
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
	| Would you like errors to be logged? Error logging can be extremely
	| helpful when debugging a production application.
	|
	*/

	'log' => false,

	/*
	|--------------------------------------------------------------------------
	| Error Logger
	|--------------------------------------------------------------------------
	|
	| Because of the sundry ways of managing error logging, you get complete
	| flexibility to manage error logging as you see fit.
	|
	| This function will be called when an error occurs in your application.
	| You can log the error however you like.
	|
	| The error "severity" passed to the method is a human-readable severity
	| level such as "Parsing Error", "Fatal Error", etc.
	|
	| A simple logging system has been setup for you. By default, all errors
	| will be logged to the application/log.txt file.
	|
	*/

	'logger' => function($severity, $message)
	{
		System\File::append(APP_PATH.'storage/log.txt', date('Y-m-d H:i:s').' '.$severity.' - '.$message.PHP_EOL);
	},

);