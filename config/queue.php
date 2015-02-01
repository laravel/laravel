<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Default Queue Driver
	|--------------------------------------------------------------------------
	|
	| The Laravel queue API supports a variety of back-ends via an unified
	| API, giving you convenient access to each back-end using the same
	| syntax for each one. Here you may set the default queue driver.
	|
	| Supported: "null", "sync", "database", "beanstalkd",
	|            "sqs", "iron", "redis"
	|
	*/

	'default' => env('QUEUE_DRIVER', 'sync'),

	/*
	|--------------------------------------------------------------------------
	| Queue Connections
	|--------------------------------------------------------------------------
	|
	| Here you may configure the connection information for each server that
	| is used by your application. A default configuration has been added
	| for each back-end shipped with Laravel. You are free to add more.
	|
	*/

	'connections' => [

		'sync' => [
			'driver' => 'sync',
		],

		'database' => [
			'driver' => 'database',
			'table'  => 'jobs',
			'queue'  => env('QUEUE_DB_QUEUE', 'default'),
			'expire' => 60,
		],

		'beanstalkd' => [
			'driver' => 'beanstalkd',
			'host'   => env('QUEUE_BEANSTALKD_HOST', 'localhost'),
			'queue'  => env('QUEUE_BEANSTALKD_QUEUE', 'default'),
			'ttr'    => 60,
		],

		'sqs' => [
			'driver' => 'sqs',
			'key'    => env('QUEUE_SQS_KEY', 'your-public-key'),
			'secret' => env('QUEUE_SQS_SECRET', 'your-secret-key'),
			'queue'  => env('QUEUE_SQS_QUEUE', 'your-queue-url'),
			'region' => env('QUEUE_SQS_REGION', 'us-east-1'),
		],

		'iron' => [
			'driver'  => 'iron',
			'host'    => env('QUEUE_IRON_HOST', 'mq-aws-us-east-1.iron.io'),
			'token'   => env('QUEUE_IRON_TOKEN', 'your-token'),
			'project' => env('QUEUE_IRON_PROJECT', 'your-project-id'),
			'queue'   => env('QUEUE_IRON_QUEUE', 'your-queue-name'),
			'encrypt' => env('QUEUE_IRON_ENCRYPT', true),
		],

		'redis' => [
			'driver' => 'redis',
			'queue'  => env('QUEUE_REDIS_QUEUE', 'default'),
			'expire' => 60,
		],

	],

	/*
	|--------------------------------------------------------------------------
	| Failed Queue Jobs
	|--------------------------------------------------------------------------
	|
	| These options configure the behavior of failed queue job logging so you
	| can control which database and table are used to store the jobs that
	| have failed. You may change them to any database / table you wish.
	|
	*/

	'failed' => [
		'database' => env('DB_DEFAULT', 'mysql'), 'table' => 'failed_jobs',
	],

];
