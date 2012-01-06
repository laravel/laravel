<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Default Database Connection
	|--------------------------------------------------------------------------
	|
	| The name of your default database connection.
	|
	| This connection will be the default for all database operations unless a
	| different connection is specified when performing the operation.
	|
	*/

	'default' => 'mysql',

	/*
	|--------------------------------------------------------------------------
	| Database Connections
	|--------------------------------------------------------------------------
	|
	| All of the database connections used by your application.
	|
	| Supported Drivers: 'mysql', 'pgsql', 'sqlsrv', 'sqlite'.
	|
	| When using the SQLite driver, the path and "sqlite" extention will be
	| added automatically. You only need to specify the database name.
	|
	*/

	'connections' => array(

		'sqlite' => array(
			'driver'   => 'sqlite',
			'database' => 'application',
		),

		'mysql' => array(
			'driver'   => 'mysql',
			'host'     => 'localhost',
			'database' => 'database',
			'username' => 'root',
			'password' => 'password',
			'charset'  => 'utf8',
		),

		'pgsql' => array(
			'driver'   => 'pgsql',
			'host'     => 'localhost',
			'database' => 'database',
			'username' => 'root',
			'password' => 'password',
			'charset'  => 'utf8',
		),

		'sqlsrv' => array(
			'driver'   => 'sqlsrv',
			'host'     => 'localhost',
			'database' => 'database',
			'username' => 'root',
			'password' => 'password',
		),

	),

	/*
	|--------------------------------------------------------------------------
	| Redis Databases
	|--------------------------------------------------------------------------
	|
	| Redis is an open source, fast, and advanced key-value store. However, it
	| provides a richer set of commands than a typical key-value store such as
	| APC or memcached.
	|
	| Here you may specify the hosts and ports for your Redis databases.
	|
	| For more information regarding Redis, check out: http://redis.io
	|
	*/

	'redis' => array(

		'default' => array('host' => '127.0.0.1', 'port' => 6379),

	),

);