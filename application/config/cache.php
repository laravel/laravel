<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Cache Driver
	|--------------------------------------------------------------------------
	|
	| The name of the default cache driver for your application.
	|
	| Caching can be used to increase the performance of your application
	| by storing commonly accessed data in memory or in a file.
	|
	| Supported Drivers: 'file', 'memcached', 'apc', 'redis', 'database'.
	|
	*/

	'driver' => 'file',

	/*
	|--------------------------------------------------------------------------
	| Cache Key
	|--------------------------------------------------------------------------
	|
	| This key will be prepended to item keys stored using Memcached and APC to
	| prevent collisions with other applications on the server.
	|
	*/

	'key' => 'laravel',

	/*
	|--------------------------------------------------------------------------
	| Cache Database
	|--------------------------------------------------------------------------
	|
	| When using the database cache driver, this database table will be used
	| to store the cached item. You may also add a "connection" option to
	| the array to specify which database connection should be used.
	|
	*/

	'database' => array('table' => 'laravel_cache'),

	/*
	|--------------------------------------------------------------------------
	| Memcached Servers
	|--------------------------------------------------------------------------
	|
	| The Memcached servers used by your application.
	|
	| Memcached is a free and open source, high-performance, distributed memory
	| object caching system, generic in nature, but intended for use in speeding
	| up dynamic web applications by alleviating database load.
	|
	| For more information about Memcached, check out: http://memcached.org
	|
	*/

	'memcached' => array(

		array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100),

	),	

);