<?php namespace Laravel;

return array(

	/*
	|--------------------------------------------------------------------------
	| Laravel Routing Components
	|--------------------------------------------------------------------------
	|
	| The following components are used by the Laravel routing system.
	|
	| The router is used to map a given method and URI to a route intance.
	|
	| The route loader is responsible for loading the appropriates routes file
	| for a given request URI, as well as loading all routes when the framework
	| needs to find a named route wtihin the application.
	|
	| The route caller is responsible for receiving a route and taking the
	| appropriate action to execute that route. Some routes delegate execution
	| to a controller, so this class will also resolve controllers out of the
	| container and call the appropriate methods on those controllers.
	|
	*/

	'laravel.routing.router' => array('singleton' => true, 'resolver' => function($c)
	{
		return new Routing\Router($c->core('routing.loader'), CONTROLLER_PATH);
	}),


	'laravel.routing.loader' => array('singleton' => true, 'resolver' => function($c)
	{
		return new Routing\Loader(APP_PATH, ROUTE_PATH);
	}),


	'laravel.routing.caller' => array('resolver' => function($c)
	{
		return new Routing\Caller($c, require APP_PATH.'filters'.EXT, CONTROLLER_PATH);
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel Database Connectors
	|--------------------------------------------------------------------------
	|
	| The following components are used to establish PDO database connections
	| to the various database systems supported by Laravel. By resolving these
	| connectors out of the IoC container, new database systems may be added
	| by simply registering a connector in the container.
	|
	*/

	'laravel.database.connectors.sqlite' => array('resolver' => function($c)
	{
		return new Database\Connectors\SQLite;
	}),

	'laravel.database.connectors.mysql' => array('resolver' => function($c)
	{
		return new Database\Connectors\MySQL;
	}),

	'laravel.database.connectors.pgsql' => array('resolver' => function($c)
	{
		return new Database\Connectors\Postgres;
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel Caching Components
	|--------------------------------------------------------------------------
	|
	| The following components are used by the wonderfully, simple Laravel
	| caching system. Each driver is resolved through the container.
	|
	| New cache drivers may be added to the framework by simply registering
	| them into the container.
	|
	*/

	'laravel.cache.apc' => array('resolver' => function($c)
	{
		return new Cache\Drivers\APC(Config::get('cache.key'));
	}),


	'laravel.cache.file' => array('resolver' => function($c)
	{
		return new Cache\Drivers\File(CACHE_PATH);
	}),


	'laravel.cache.memcached' => array('resolver' => function($c)
	{
		return new Cache\Drivers\Memcached($c->core('cache.memcache.connection'), Config::get('cache.key'));
	}),


	'laravel.cache.memcache.connection' => array('singleton' => true, 'resolver' => function($c)
	{
		$memcache = new \Memcache;

		foreach (Config::get('cache.memcached') as $server)
		{
			$memcache->addServer($server['host'], $server['port'], true, $server['weight']);
		}

		if ($memcache->getVersion() === false)
		{
			throw new \Exception('Could not establish memcached connection. Please verify your memcached configuration.');
		}

		return $memcache;
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel Session Components
	|--------------------------------------------------------------------------
	|
	| The following components are used by the Laravel session system.
	|
	| The framework allows the session ID to be transported via a variety
	| of different mechanisms by resolve the ID itself and the session
	| transporter instance out of the container. This allows sessions
	| to be used by clients who cannot receive cookies.
	|
	| The session manager is responsible for loading the session payload
	| from the session driver, as well as examining the payload validitiy
	| and things like the CSRF token.
	|
	| Like the caching components, each session driver is resolved via the
	| container and new drivers may be added by registering them into the
	| container. Several session drivers are "driven" by the cache drivers.
	|
	*/

	'laravel.session.transporter' => array('resolver' => function($c)
	{
		return new Session\Transporters\Cookie;
	}),


	'laravel.session.apc' => array('resolver' => function($c)
	{
		return new Session\Drivers\APC($c->core('cache.apc'));
	}),


	'laravel.session.cookie' => array('resolver' => function($c)
	{
		return new Session\Drivers\Cookie;
	}),


	'laravel.session.database' => array('resolver' => function($c)
	{
		return new Session\Drivers\Database(Database\Manager::connection());
	}),


	'laravel.session.file' => array('resolver' => function($c)
	{
		return new Session\Drivers\File(SESSION_PATH);
	}),


	'laravel.session.memcached' => array('resolver' => function($c)
	{
		return new Session\Drivers\Memcached($c->core('cache.memcached'));
	}),

);