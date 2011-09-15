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
	| different connection is specified when performing the operation. The name
	| of the default connection should correspond to the name of a connector
	| defined below.
	|
	*/

	'default' => 'sqlite',

	/*
	|--------------------------------------------------------------------------
	| Database Connectors
	|--------------------------------------------------------------------------
	|
	| All of the database connectors used by your application.
	|
	| Each connector should return a PDO connection. You may connect to any
	| database system you wish. Of course, default configurations for the
	| systems supported by Laravel are provided for you.
	|
	| The entire database configuration array is passed to the connector
	| closure, so you may convenient use it when connecting to your database.
	|
	| Note: When using an unsupported database, Eloquent and the fluent query
	|       builder may not work as expected. Currently, MySQL, Postgres, and
	|       SQLite are fully supported by Laravel.
	|
	*/

	'connectors' => array(

		'sqlite' => function($config)
		{
			return new PDO('sqlite:'.DATABASE_PATH.'application.sqlite', null, null, $config['options']);
		},

		'mysql' => function($config)
		{
			return new PDO('mysql:host=localhost;dbname=database', 'root', 'password', $config['options']);
		},

		'pgsql' => function($config)
		{
			return new PDO('pgsql:host=localhost;dbname=database', 'root', 'password', $config['options']);
		},

	),

	/*
	|--------------------------------------------------------------------------
	| Database PDO Options
	|--------------------------------------------------------------------------
	|
	| Here you may specify the PDO options that should be used when connecting
	| to a database. The entire database configuration array is passed to the
	| database connector closures, so may convenient access these options from
	| your connectors.
	|
	| For a list of options, visit: http://php.net/manual/en/pdo.setattribute.php
	|
	*/

	'options' => array(
		PDO::ATTR_CASE              => PDO::CASE_LOWER,
		PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
		PDO::ATTR_STRINGIFY_FETCHES => false,
		PDO::ATTR_EMULATE_PREPARES  => false,
	),

);