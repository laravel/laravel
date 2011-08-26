<?php namespace Laravel;

return array(

	/*
	|--------------------------------------------------------------------------
	| Laravel Support Components
	|--------------------------------------------------------------------------
	*/

	'laravel.file' => array('singleton' => true, 'resolver' => function()
	{
		return new File;
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel View Components
	|--------------------------------------------------------------------------
	*/

	'laravel.composers' => array('singleton' => true, 'resolver' => function()
	{
		return require APP_PATH.'composers'.EXT;
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel Routing Components
	|--------------------------------------------------------------------------
	*/

	'laravel.routing.router' => array('singleton' => true, 'resolver' => function($container)
	{
		return new Routing\Router($container->resolve('laravel.request'), require APP_PATH.'routes'.EXT);
	}),

	'laravel.routing.handler' => array('resolver' => function($container)
	{
		return new Routing\Handler($container->resolve('laravel.request'), require APP_PATH.'filters'.EXT);
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel Security Components
	|--------------------------------------------------------------------------
	*/

	'laravel.security.auth' => array('resolver' => function($container)
	{
		$hasher = $container->resolve('laravel.security.hashing.engine');

		return new Security\Auth(Session\Manager::driver(), $hasher);
	}),

	'laravel.security.hashing.engine' => array('resolver' => function()
	{
		return new Security\Hashing\BCrypt(10, false);
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel Session Components
	|--------------------------------------------------------------------------
	*/

	'laravel.session.driver' => array('resolver' => function()
	{
		return Session\Manager::driver();
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel Cookie Session Components
	|--------------------------------------------------------------------------
	*/

	'laravel.session.cookie' => array('resolver' => function($container)
	{
		return new Session\Cookie(Security\Crypter::make(), $container->resolve('laravel.request')->input->cookies);
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel Database Session Components
	|--------------------------------------------------------------------------
	*/

	'laravel.session.database' => array('resolver' => function($container)
	{
		return new Session\Database($container->resolve('laravel.session.database.connection'));
	}),

	'laravel.session.database.connection' => array('resolver' => function()
	{
		return Database\Manager::connection();
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel File Cache & Session Components
	|--------------------------------------------------------------------------
	*/

	'laravel.cache.file' => array('resolver' => function()
	{
		return new Cache\File(new File);
	}),

	'laravel.session.file' => array('resolver' => function()
	{
		return new Session\File(new File);
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel APC Cache & Session Components
	|--------------------------------------------------------------------------
	*/

	'laravel.cache.apc' => array('resolver' => function($container)
	{
		return new Cache\APC($container->resolve('laravel.cache.apc_engine'));
	}),

	'laravel.cache.apc_engine' => array('resolver' => function()
	{
		return new Cache\APC_Engine;
	}),

	'laravel.session.apc' => array('resolver' => function($container)
	{
		return new Session\APC($container->resolve('laravel.cache.apc'));
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel Memcached Cache & Session Components
	|--------------------------------------------------------------------------
	*/

	'laravel.cache.memcached' => array('resolver' => function($container)
	{
		return new Cache\Memcached($container->resolve('laravel.memcache'));
	}),

	'laravel.session.memcached' => array('resolver' => function($container)
	{
		return new Session\Memcached($container->resolve('laravel.cache.memcached'));
	}),

	'laravel.memcache' => array('singleton' => true, 'resolver' => function()
	{
		if ( ! class_exists('Memcache'))
		{
			throw new \Exception('Attempting to use Memcached, but the Memcache PHP extension is not installed on this server.');
		}

		$memcache = new \Memcache;

		foreach (Config::get('cache.servers') as $server)
		{
			$memcache->addServer($server['host'], $server['port'], true, $server['weight']);
		}

		if ($memcache->getVersion() === false)
		{
			throw new \Exception('Memcached is configured. However, no connections could be made. Please verify your memcached configuration.');
		}

		return $memcache;
	}),

);