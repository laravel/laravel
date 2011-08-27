<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Error Handler
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

	'handler' => function($exception)
	{
		var_dump($exception);

		exit(1);
	},

);