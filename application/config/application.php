<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Application URL
	|--------------------------------------------------------------------------
	|
	| The URL used to access your application without a trailing slash. The URL
	| does not have to be set. If it isn't we'll try our best to guess the URL
	| of your application.
	|
	*/

	'url' => '',

	/*
	|--------------------------------------------------------------------------
	| Application Index
	|--------------------------------------------------------------------------
	|
	| If you are including the "index.php" in your URLs, you can ignore this.
	| However, if you are using mod_rewrite to get cleaner URLs, just set
	| this option to an empty string and we'll take care of the rest.
	|
	*/

	'index' => 'index.php',

	/*
	|--------------------------------------------------------------------------
	| Application Key
	|--------------------------------------------------------------------------
	|
	| This key is used by the encryption and cookie classes to generate secure
	| encrypted strings and hashes. It is extremely important that this key
	| remain secret and should not be shared with anyone. Make it about 32
	| characters of random gibberish.
	|
	| The "auto_key" option tells Laravel to automatically set this key value
	| if one has not already been set. This is generally done on the first
	| request to the Laravel splash screen.
	|
	*/

	'key' => '',

	'auto_key' => true,

	/*
	|--------------------------------------------------------------------------
	| Application Character Encoding
	|--------------------------------------------------------------------------
	|
	| The default character encoding used by your application. This encoding
	| will be used by the Str, Text, Form, and any other classes that need
	| to know what type of encoding to use for your awesome application.
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
	| SSL Link Generation
	|--------------------------------------------------------------------------
	|
	| Many sites use SSL to protect their users data. However, you may not be
	| able to use SSL on your development machine, meaning all HTTPS will be
	| broken during development.
	|
	| For this reason, you may wish to disable the generation of HTTPS links
	| throughout your application. This option does just that. All attempts
	| to generate HTTPS links will generate regular HTTP links instead.
	|
	*/

	'ssl' => true,

	/*
	|--------------------------------------------------------------------------
	| Application Timezone
	|--------------------------------------------------------------------------
	|
	| The default timezone of your application. The timezone will be used when
	| Laravel needs a date, such as when writing to a log file or travelling
	| to a distant star at warp speed.
	|
	*/

	'timezone' => 'UTC',

	/*
	|--------------------------------------------------------------------------
	| Class Aliases
	|--------------------------------------------------------------------------
	|
	| Here, you can specify any class aliases that you would like registered
	| when Laravel loads. Aliases are lazy-loaded, so feel free to add!
	|
	| Aliases make it more convenient to use namespaced classes. Instead of
	| referring to the class using its full namespace, you may simply use
	| the alias defined here.
	|
	*/

	'aliases' => array(
		'Auth'       => 'Laravel\\Auth',
		'Asset'      => 'Laravel\\Asset',
		'Autoloader' => 'Laravel\\Autoloader',
		'Bundle'     => 'Laravel\\Bundle',
		'Cache'      => 'Laravel\\Cache',
		'Config'     => 'Laravel\\Config',
		'Controller' => 'Laravel\\Routing\\Controller',
		'Cookie'     => 'Laravel\\Cookie',
		'Crypter'    => 'Laravel\\Crypter',
		'DB'         => 'Laravel\\Database',
		'Event'      => 'Laravel\\Event',
		'File'       => 'Laravel\\File',
		'Filter'     => 'Laravel\\Routing\\Filter',
		'Form'       => 'Laravel\\Form',
		'Hash'       => 'Laravel\\Hash',
		'HTML'       => 'Laravel\\HTML',
		'Input'      => 'Laravel\\Input',
		'IoC'        => 'Laravel\\IoC',
		'Lang'       => 'Laravel\\Lang',
		'Log'        => 'Laravel\\Log',
		'Memcached'  => 'Laravel\\Memcached',
		'Paginator'  => 'Laravel\\Paginator',
		'URL'        => 'Laravel\\URL',
		'Redirect'   => 'Laravel\\Redirect',
		'Redis'      => 'Laravel\\Redis',
		'Request'    => 'Laravel\\Request',
		'Response'   => 'Laravel\\Response',
		'Route'      => 'Laravel\\Routing\\Route',
		'Router'     => 'Laravel\\Routing\\Router',
		'Schema'     => 'Laravel\\Database\\Schema',
		'Section'    => 'Laravel\\Section',
		'Session'    => 'Laravel\\Session',
		'Str'        => 'Laravel\\Str',
		'Task'       => 'Laravel\\CLI\\Tasks\\Task',
		'URI'        => 'Laravel\\URI',
		'Validator'  => 'Laravel\\Validator',
		'View'       => 'Laravel\\View',
	),

);