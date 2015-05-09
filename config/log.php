<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Log path
	|--------------------------------------------------------------------------
	|
	| The location of the log files produced by using the Log facade
	|
	*/

	'path'      => ENV('LOG_PATH', storage_path('/logs/laravel.log')),

	/*
	|--------------------------------------------------------------------------
	| Maximum log files
	|--------------------------------------------------------------------------
	|
	| The maximum number of log files to keep.
	| Laravel will log to files that are rotated every day and a limited
	| number of files are kept.
	|
	| For more details, refer to the RotatingFileHandler class in Monolog.
	|
	*/

	'max_files' => ENV('LOG_MAX_FILES', 5)

];
