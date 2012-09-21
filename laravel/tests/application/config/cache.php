<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Cache Driver
	|--------------------------------------------------------------------------
	|
	| The name of the default cache driver for your application. Caching can
	| be used to increase the performance of your application by storing any
	| commonly accessed data in memory, a file, or some other storage.
	|
	| A variety of awesome drivers are available for you to use with Laravel.
	| Some, like APC, are extremely fast. However, if that isn't an option
	| in your environment, try file or database caching.
	|
	| Drivers: 'file', 'memcached', 'apc', 'redis', 'database'.
	|
	*/

	'driver' => 'file',

	/*
	|--------------------------------------------------------------------------
	| Cache Key
	|--------------------------------------------------------------------------
	|
	| This key will be prepended to item keys stored using Memcached and APC
	| to prevent collisions with other applications on the server. Since the
	| memory based stores could be shared by other applications, we need to
	| be polite and use a prefix to uniquely identifier our items.
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
	| The Memcached servers used by your application. Memcached is a free and
	| open source, high-performance, distributed memory caching system. It is
	| generic in nature but intended for use in speeding up web applications
	| by alleviating database load.
	|
	| For more information, check out: http://memcached.org
	|
	*/

	'memcached' => array(

		array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100),

	),	

);