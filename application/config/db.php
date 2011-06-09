<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Default Database Connection
	|--------------------------------------------------------------------------
	|
	| The name of your default database connection.
	|
	| This connection will be used by default for all database operations
	| unless a different connection is specified when performing the operation.
	|
	*/

	'default' => 'sqlite',

	/*
	|--------------------------------------------------------------------------
	| Database Connections
	|--------------------------------------------------------------------------
	|
	| Here you can define all of the databases used by your application.
	|
	| Supported Drivers: 'mysql', 'pgsql', 'sqlite'.
	|
	| Note: When using the SQLite driver, the path and "sqlite" extention will
	|       be added automatically. You only need to specify the database name.
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

	),

);