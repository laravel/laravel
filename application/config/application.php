<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Application URL
	|--------------------------------------------------------------------------
	|
	| The URL used to access your application. The trailing slash is optional.
	|
	| Note: Remove "index.php" from this URL when using mod_rewrite.
	|
	*/

	'url' => 'http://localhost/index.php',

	/*
	|--------------------------------------------------------------------------
	| Application Language
	|--------------------------------------------------------------------------
	|
	| The default language of your application. This language will be used by
	| default by the Lang library when doing string localization.
	|
	| If you are not using the Lang library, this option isn't really important.
	|
	*/

	'language' => 'en',

	/*
	|--------------------------------------------------------------------------
	| Application Timezone
	|--------------------------------------------------------------------------
	|
	| The default timezone of your application. This timezone will be used when
	| Laravel needs a date, such as when writing to a log file.
	|
	*/

	'timezone' => 'UTC',

	/*
	|--------------------------------------------------------------------------
	| Application Key
	|--------------------------------------------------------------------------
	|
	| Your application key should be a 32 character string that is totally
	| random and secret. This key is used by the encryption class to generate
	| secure, encrypted strings.
	|
	| If you will not be using the encryption class, this doesn't matter.
	|
	*/

	'key' => '',

	/*
	|--------------------------------------------------------------------------
	| Class Aliases
	|--------------------------------------------------------------------------
	|
	| Here, you can specify any class aliases that you would like registered
	| when Laravel loads. Aliases are lazy-loaded, so add as many as you want.
	|
	| We have already setup a few to make your life easier.
	|
	*/

	'aliases' => array(
		'Auth' => 'System\\Auth',
		'Benchmark' => 'System\\Benchmark',
		'Cache' => 'System\\Cache',
		'Config' => 'System\\Config',
		'Cookie' => 'System\\Cookie',
		'Crypt' => 'System\\Crypt',
		'Date' => 'System\\Date',
		'DB' => 'System\\DB',
		'Download' => 'System\\Download',
		'Eloquent' => 'System\\DB\\Eloquent',
		'Form' => 'System\\Form',
		'Hash' => 'System\\Hash',
		'HTML' => 'System\\HTML',
		'Inflector' => 'System\\Inflector',
		'Input' => 'System\\Input',
		'Lang' => 'System\\Lang',
		'Log' => 'System\\Log',
		'URL' => 'System\\URL',
		'Redirect' => 'System\\Redirect',
		'Request' => 'System\\Request',
		'Response' => 'System\\Response',
		'Session' => 'System\\Session',
		'Str' => 'System\\Str',
		'Test' => 'System\\Test',
		'Text' => 'System\\Text',
		'View' => 'System\View',
	),

);