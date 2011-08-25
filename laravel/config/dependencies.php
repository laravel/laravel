<?php namespace Laravel;

return array(

	/*
	|--------------------------------------------------------------------------
	| Laravel File Cache Driver
	|--------------------------------------------------------------------------
	*/

	'laravel.cache.file' => array('resolver' => function($container)
	{
		return new Cache\File($container->resolve('laravel.cache.file_engine'));
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel File Cache Driver Engine
	|--------------------------------------------------------------------------
	*/

	'laravel.cache.file_engine' => array('resolver' => function($container)
	{
		return new Cache\File_Engine;
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel APC Cache Driver
	|--------------------------------------------------------------------------
	*/

	'laravel.cache.apc' => array('resolver' => function($container)
	{
		return new Cache\APC($container->resolve('laravel.cache.apc_engine'));
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel APC Cache Driver Engine
	|--------------------------------------------------------------------------
	*/

	'laravel.cache.apc_engine' => array('resolver' => function($container)
	{
		return new Cache\APC_Engine;
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel Memcached Cache Driver
	|--------------------------------------------------------------------------
	*/

	'laravel.cache.memcached' => array('resolver' => function($container)
	{
		return new Cache\Memcached($container->resolve('laravel.memcache'));
	}),

	/*
	|--------------------------------------------------------------------------
	| Memcache Connection
	|--------------------------------------------------------------------------
	*/

	'laravel.memcache' => array('singleton' => true, 'resolver' => function($container)
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