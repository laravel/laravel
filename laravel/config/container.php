<?php namespace Laravel;

return array(

	'laravel.view.composers' => array('singleton' => true, 'resolver' => function()
	{
		return require APP_PATH.'composers'.EXT;
	}),


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