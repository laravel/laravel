<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Application Log Location
	|--------------------------------------------------------------------------
	|
	| We need a location where the application logs can be stored. A sensible
	| default has been specified, but you are free to change it to any other
	| place on disk that you desire.
	|
	*/

	'path' => storage_path().'/logs',

	/*
	|--------------------------------------------------------------------------
	| Log File
	|--------------------------------------------------------------------------
	|
	| The filename for the application logs.  Again, a sensible default has
	| been provided, but feel free to change it.  If you are using daily logs
	| (see below), then the date will be added to the file name (before the
	| file extension).
	*/

	'file' => 'laravel.log',

	/*
	|--------------------------------------------------------------------------
	| Use Daily Files
	|--------------------------------------------------------------------------
	|
	| By default, Laravel uses the same log file continuously, and you are
	| responsible for rotating it to keep it from getting too big.
	| Alternatively, by setting this value to "true", we will create a new log
	| file each day and name it appropriately (eg. "laravel-2014-02-01.log").
	|
	*/

	'daily' => true,

	/*
	|--------------------------------------------------------------------------
	| Maximum Number of Days to Keep Logs
	|--------------------------------------------------------------------------
	|
	| If you are using daily log files (above), then set this to specify how
	| many days worth of logs to keep.  Set it to zero to keep the daily logs
	| indefinitely.
	|
	*/

	'keep' => 30,

);
