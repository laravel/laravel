<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Session Driver
	|--------------------------------------------------------------------------
	|
	| The name of the session driver for your application.
	|
	| Since HTTP is stateless, sessions are used to maintain "state" across
	| multiple requests from the same user of your application.
	|
	| Supported Drivers: 'file', 'db', 'memcached', 'apc'.
	|
	*/

	'driver' => 'file',

	/*
	|--------------------------------------------------------------------------
	| Session Database
	|--------------------------------------------------------------------------
	|
	| The database table on which the session should be stored. 
	|
	| If you are not using database based sessions, don't worry about this.
	|
	*/

	'table' => 'sessions',

	/*
	|--------------------------------------------------------------------------
	| Session Lifetime
	|--------------------------------------------------------------------------
	|
	| How many minutes can a session be idle before expiring?
	|
	*/

	'lifetime' => 60,

	/*
	|--------------------------------------------------------------------------
	| Session Expiration On Close
	|--------------------------------------------------------------------------
	|
	| Should the session expire when the user's web browser closes?
	|
	*/

	'expire_on_close' => false,

	/*
	|--------------------------------------------------------------------------
	| Session Cookie Path
	|--------------------------------------------------------------------------
	|
	| The path for which the session cookie is available.
	|
	*/

	'path' => '/',

	/*
	|--------------------------------------------------------------------------
	| Session Cookie Domain
	|--------------------------------------------------------------------------
	|
	| The domain for which the session cookie is available.
	|
	*/

	'domain' => null,

	/*
	|--------------------------------------------------------------------------
	| Session Cookie HTTPS
	|--------------------------------------------------------------------------
	|
	| Should the session cookie only be transported over HTTPS?
	|
	*/

	'https' => false,

	/*
	|--------------------------------------------------------------------------
	| HTTP Only Session Cookie
	|--------------------------------------------------------------------------
	|
	| Should the session cookie only be accessible over HTTP?
	|
	| Note: The intention of the "HTTP Only" option is to keep cookies from
	|       being accessed by client-side scripting languages. However, this
	|       setting should not be viewed as providing total XSS protection.
	|
	*/

	'http_only' => false,

);