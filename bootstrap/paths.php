<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Application Path
	|--------------------------------------------------------------------------
	|
	| Here we just defined the path to the application directory. Most likely
	| you will never need to change this value as the default setup should
	| work perfectly fine for the vast majority of all our applications.
	|
	*/

	'app' => __DIR__.'/../app',

	/*
	|--------------------------------------------------------------------------
	| Public Path
	|--------------------------------------------------------------------------
	|
	| The public path contains the assets for your web application, such as
	| your JavaScript and CSS files, and also contains the primary entry
	| point for web requests into these applications from the outside.
	|
	*/

	'public' => __DIR__.'/../public',

	/*
	|--------------------------------------------------------------------------
	| Base Path
	|--------------------------------------------------------------------------
	|
	| The base path is the root of the Laravel installation. Most likely you
	| will not need to change this value. But, if for some wild reason it
	| is necessary you will do so here, just proceed with some caution.
	|
	*/

	'base' => __DIR__.'/..',

	/*
	|--------------------------------------------------------------------------
	| Storage Path
	|--------------------------------------------------------------------------
	|
	| The storage path is used by Laravel to store cached Blade views, logs
	| and other pieces of information. You may modify the path here when
	| you want to change the location of this directory for your apps.
	|
	*/

	'storage' => __DIR__.'/../storage',

	/*
	|--------------------------------------------------------------------------
	| Generator Paths
	|--------------------------------------------------------------------------
	|
	| These paths are used by the various class generators and other pieces
	| of the framework that need to determine where to store these types
	| of classes. Of course, they may be changed to any path you wish.
	|
	*/

	'commands' => __DIR__.'/../app/console',
	'config' => __DIR__.'/../app/config',
	'controllers' => __DIR__.'/../app/http/controllers',
	'database' => __DIR__.'/../app/database',
	'filters' => __DIR__.'/../app/http/filters',
	'lang' => __DIR__.'/../app/lang',
	'requests' => __DIR__.'/../app/http/requests',

);
