<?php

return [

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
	| Configuration Path
	|--------------------------------------------------------------------------
	|
	| This path is used by the configuration loader to load the application
	| configuration files. In general, you should'nt need to change this
	| value; however, you can theoretically change the path from here.
	|
	*/

	'config' => __DIR__.'/../config',

	/*
	|--------------------------------------------------------------------------
	| Database Path
	|--------------------------------------------------------------------------
	|
	| This path is used by the migration generator and migration runner to
	| know where to place your fresh database migration classes. You're
	| free to modify the path but you probably will not ever need to.
	|
	*/

	'database' => __DIR__.'/../database',

	/*
	|--------------------------------------------------------------------------
	| Language Path
	|--------------------------------------------------------------------------
	|
	| This path is used by the language file loader to load your application
	| language files. The purpose of these files is to store your strings
	| that are translated into other languages for views, e-mails, etc.
	|
	*/

	'lang' => __DIR__.'/../resources/lang',

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
	| Storage Path
	|--------------------------------------------------------------------------
	|
	| The storage path is used by Laravel to store cached Blade views, logs
	| and other pieces of information. You may modify the path here when
	| you want to change the location of this directory for your apps.
	|
	*/

	'storage' => __DIR__.'/../storage',

];
