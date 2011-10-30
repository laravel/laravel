<?php namespace Laravel;

return array(

	/*
	|--------------------------------------------------------------------------
	| Various Laravel Components
	|--------------------------------------------------------------------------
	|
	| Many of the Laravel classes are resolved through the inversion of control
	| container to maintain a high level of testability and flexibility.
	|
	| Most of them are also accessible through a "Facade", which simulates the
	| class being usable via static methods for convenience. Facades allow the
	| framework to keep a convenient API, while still having a testable core.
	|
	*/

	'laravel.input' => array('singleton' => true, 'resolver' => function($c)
	{
		require SYS_PATH.'input'.EXT;

		$input = array();

		$request = $c->core('request');

		switch ($request->method())
		{
			case 'GET':
				$input = $_GET;
				break;

			case 'POST':
				$input = $_POST;
				break;

			case 'PUT':
			case 'DELETE':
				if ($request->spoofed())
				{
					$input = $_POST;
				}
				else
				{
					parse_str(file_get_contents('php://input'), $input);
				}
		}

		return new Input($input, $_FILES);
	}),


	'laravel.request' => array('singleton' => true, 'resolver' => function($c)
	{
		require_once SYS_PATH.'request'.EXT;

		return new Request($c->core('uri'), $_POST, $_SERVER);
	}),


	'laravel.uri' => array('singleton' => true, 'resolver' => function($c)
	{
		require_once SYS_PATH.'uri'.EXT;

		return new URI($_SERVER);
	}),

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
		return new Database\Connectors\SQLite(DATABASE_PATH);
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
	| The following components are used by the wonderfully simple Laravel cache
	| system. Each driver is resolved through the container, so new drivers may
	| be added by simply registering them in the container.
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


	'laravel.cache.redis' => array('resolver' => function()
	{
		return new Cache\Drivers\Redis(Redis::db());		
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


	'laravel.session.redis' => array('resolver' => function($c)
	{
		return new Session\Drivers\Redis($c->core('cache.redis'));
	}),


	'laravel.session.memcached' => array('resolver' => function($c)
	{
		return new Session\Drivers\Memcached($c->core('cache.memcached'));
	}),

);