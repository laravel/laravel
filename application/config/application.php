<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Application URL
	|--------------------------------------------------------------------------
	|
	| The URL used to access your application. No trailing slash.
	|
	*/

	'url' => 'http://localhost',

	/*
	|--------------------------------------------------------------------------
	| Application Index
	|--------------------------------------------------------------------------
	|
	| If you are including the "index.php" in your URLs, you can ignore this.
	|
	| However, if you are using mod_rewrite or something similar to get
	| cleaner URLs, set this option to an empty string.
	|
	*/

	'index' => 'index.php',

	/*
	|--------------------------------------------------------------------------
	| Application Key
	|--------------------------------------------------------------------------
	|
	| The application key should be a random, 32 character string.
	|
	| This key is used by the encryption and cookie classes to generate secure
	| encrypted strings and hashes. It is extremely important that this key
	| remain secret and should not be shared with anyone.
	|
	*/

	'key' => '',

	/*
	|--------------------------------------------------------------------------
	| Application Character Encoding
	|--------------------------------------------------------------------------
	|
	| The default character encoding used by your application. This is the
	| character encoding that will be used by the Str, Text, and Form classes.
	|
	*/

	'encoding' => 'UTF-8',

	/*
	|--------------------------------------------------------------------------
	| Application Language
	|--------------------------------------------------------------------------
	|
	| The default language of your application. This language will be used by
	| Lang library as the default language when doing string localization.
	|
	*/

	'language' => 'en',

	/*
	|--------------------------------------------------------------------------
	| Auto-Loaded Packages
	|--------------------------------------------------------------------------
	|
	| The packages that should be auto-loaded each time Laravel handles a
	| request. These should generally be packages that you use on almost every
	| request to your application.
	|
	| Each package specified here will be bootstrapped and can be conveniently
	| used by your application's routes, models, and libraries.
	|
	| Note: The package names in this array should correspond to a package
	|       directory in application/packages.
	|
	*/

	'packages' => array(),

	/*
	|--------------------------------------------------------------------------
	| SSL Link Generation
	|--------------------------------------------------------------------------
	|
	| Many sites use SSL to protect their users data. However, you may not
	| always be able to use SSL on your development machine, meaning all HTTPS
	| will be broken during development.
	|
	| For this reason, you may wish to disable the generation of HTTPS links
	| throughout your application. This option does just that. All attempts to
	| generate HTTPS links will generate regular HTTP links instead.
	|
	*/

	'ssl' => true,

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
	| Class Aliases
	|--------------------------------------------------------------------------
	|
	| Here, you can specify any class aliases that you would like registered
	| when Laravel loads. Aliases are lazy-loaded, so add as many as you want.
	|
	| Aliases make it more convenient to use namespaced classes. Instead of
	| referring to the class using its full namespace, you may simply use
	| the alias defined here.
	|
	| We have already aliased common Laravel classes to make your life easier.
	|
	*/

	'aliases' => array(
		'Arr'        => 'Laravel\\Arr',
		'Asset'      => 'Laravel\\Asset',
		'Auth'       => 'Laravel\\Security\\Auth',
		'Benchmark'  => 'Laravel\\Benchmark',
		'Cache'      => 'Laravel\\Cache',
		'Config'     => 'Laravel\\Config',
		'Controller' => 'Laravel\\Routing\\Controller',
		'Cookie'     => 'Laravel\\Cookie',
		'Crypter'    => 'Laravel\\Security\\Crypter',
		'DB'         => 'Laravel\\Database\\Manager',
		'Eloquent'   => 'Laravel\\Database\\Eloquent\\Model',
		'File'       => 'Laravel\\File',
		'Form'       => 'Laravel\\Form',
		'Hasher'     => 'Laravel\\Security\\Hasher',
		'HTML'       => 'Laravel\\HTML',
		'Inflector'  => 'Laravel\\Inflector',
		'Input'      => 'Laravel\\Input',
		'IoC'        => 'Laravel\\IoC',
		'Lang'       => 'Laravel\\Lang',
		'Loader'     => 'Laravel\\Loader',
		'Messages'   => 'Laravel\\Validation\\Messages',
		'Package'    => 'Laravel\\Facades\\Package',
		'URI'        => 'Laravel\\URI',
		'URL'        => 'Laravel\\URL',
		'Redirect'   => 'Laravel\\Redirect',
		'Request'    => 'Laravel\\Request',
		'Response'   => 'Laravel\\Response',
		'Session'    => 'Laravel\\Session\\Manager',
		'Str'        => 'Laravel\\Str',
		'Validator'  => 'Laravel\\Validation\\Validator',
		'View'       => 'Laravel\\View',
	),

);