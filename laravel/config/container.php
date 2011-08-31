<?php namespace Laravel;

return array(

	/*
	|--------------------------------------------------------------------------
	| Laravel Components
	|--------------------------------------------------------------------------
	*/

	'laravel.config' => array('singleton' => true, 'resolver' => function($container)
	{
		$paths = array(SYS_CONFIG_PATH, CONFIG_PATH);

		if (isset($_SERVER['LARAVEL_ENV']))
		{
			$paths[] = CONFIG_PATH.$_SERVER['LARAVEL_ENV'].'/';
		}

		return new Config($paths);
	}),


	'laravel.cookie' => array('singleton' => true, 'resolver' => function()
	{
		return new Cookie($_COOKIE);		
	}),


	'laravel.database' => array('singleton' => true, 'resolver' => function($container)
	{
		$config = $container->resolve('laravel.config');

		return new Database\Manager($config->get('database.connections'), $config->get('database.default'));
	}),


	'laravel.download' => array('singleton' => true, 'resolver' => function($container)
	{
		return new Download($container->resolve('laravel.file'));		
	}),


	'laravel.file' => array('singleton' => true, 'resolver' => function($container)
	{
		return new File($container->resolve('laravel.config')->get('mimes'));
	}),


	'laravel.form' => array('resolver' => function($container)
	{
		list($request, $html, $url) = array(
			$container->resolve('laravel.request'),
			$container->resolve('laravel.html'),
			$container->resolve('laravel.url'),
		);

		return new Form($request, $html, $url);
	}),


	'laravel.html' => array('resolver' => function($container)
	{
		return new HTML($container->resolve('laravel.url'), $container->resolve('laravel.config')->get('application.encoding'));
	}),


	'laravel.input' => array('singleton' => true, 'resolver' => function($container)
	{
		$request = $container->resolve('laravel.request');

		$input = array();

		if ($request->method() == 'GET')
		{
			$input = $_GET;
		}
		elseif ($request->method() == 'POST')
		{
			$input = $_POST;
		}
		elseif ($request->method() == 'PUT' or $request->method == 'DELETE')
		{
			($request->spoofed()) ? $input = $_POST : parse_str(file_get_contents('php://input'), $input);
		}

		return new Input($input, $_FILES, $container->resolve('laravel.cookie'));
	}),


	'laravel.lang' => array('singleton' => true, 'resolver' => function($container)
	{
		return new Lang($container->resolve('laravel.config')->get('application.language'), array(SYS_LANG_PATH, LANG_PATH));		
	}),


	'laravel.loader' => array('singleton' => true, 'resolver' => function($container)
	{
		$paths = array(BASE_PATH, APP_PATH.'models/', APP_PATH.'libraries/');

		return new Loader($container->resolve('laravel.config')->get('aliases'), $paths);
	}),


	'laravel.package' => array('singleton' => true, 'resolver' => function()
	{
		return new Package(PACKAGE_PATH);
	}),


	'laravel.redirect' => array('singleton' => true, 'resolver' => function($container)
	{
		return new Redirect($container->resolve('laravel.url'));
	}),


	'laravel.request' => array('singleton' => true, 'resolver' => function($container)
	{
		return new Request($_SERVER, $_POST, $container->resolve('laravel.config')->get('application.url'));
	}),


	'laravel.response' => array('singleton' => true, 'resolver' => function($container)
	{
		require_once SYS_PATH.'response'.EXT;

		return new Response_Factory($container->resolve('laravel.view'));
	}),


	'laravel.router' => array('singleton' => true, 'resolver' => function($container)
	{
		return new Routing\Router($container->resolve('laravel.request'), require APP_PATH.'routes'.EXT, CONTROLLER_PATH);
	}),


	'laravel.session' => array('singleton' => true, 'resolver' => function($container)
	{
		return $container->resolve('laravel.session.manager')->driver($container->resolve('laravel.config')->get('session.driver'));
	}),


	'laravel.session.manager' => array('singleton' => true, 'resolver' => function($container)
	{
		return new Session\Manager($container);
	}),


	'laravel.url' => array('singleton' => true, 'resolver' => function($container)
	{
		list($request, $base, $index) = array(
			$container->resolve('laravel.request'),
			$container->resolve('laravel.config')->get('application.url'),
			$container->resolve('laravel.config')->get('application.index'),
		);

		return new URL($container->resolve('laravel.router'), $base, $index, $request->secure());
	}),


	'laravel.view' => array('singleton' => true, 'resolver' => function()
	{
		require_once SYS_PATH.'view'.EXT;

		return new View_Factory(VIEW_PATH, new View_Composer(require APP_PATH.'composers'.EXT));
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
	| Laravel Cookie Session Components
	|--------------------------------------------------------------------------
	*/

	'laravel.session.cookie' => array('resolver' => function($container)
	{
		$cookies = $container->resolve('laravel.request')->input->cookies;

		$config = $container->resolve('laravel.config')->get('session');

		return new Session\Cookie(Security\Crypter::make(), $cookies, $config);
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel Database Session Components
	|--------------------------------------------------------------------------
	*/

	'laravel.session.database' => array('resolver' => function($container)
	{
		$table = $container->resolve('laravel.config')->get('session.table');

		return new Session\Database($container->resolve('laravel.database.manager')->connection(), $table);
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel Cache Manager
	|--------------------------------------------------------------------------
	*/

	'laravel.cache' => array('singleton' => true, 'resolver' => function($container)
	{
		return new Cache\Manager($container, $container->resolve('laravel.config')->get('cache.driver'));
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel File Cache & Session Components
	|--------------------------------------------------------------------------
	*/

	'laravel.cache.file' => array('resolver' => function($container)
	{
		return new Cache\File($container->resolve('laravel.file'), CACHE_PATH);
	}),


	'laravel.session.file' => array('resolver' => function($container)
	{
		return new Session\File($container->resolve('laravel.file'), SESSION_PATH);
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel APC Cache & Session Components
	|--------------------------------------------------------------------------
	*/

	'laravel.cache.apc' => array('resolver' => function($container)
	{
		return new Cache\APC(new Cache\APC_Engine, $container->resolve('laravel.config')->get('cache.key'));
	}),


	'laravel.session.apc' => array('resolver' => function($container)
	{
		$lifetime = $container->resolve('laravel.config')->get('session.lifetime');

		return new Session\APC($container->resolve('laravel.cache.apc'), $lifetime);
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel Memcached Cache & Session Components
	|--------------------------------------------------------------------------
	*/

	'laravel.cache.memcached' => array('resolver' => function($container)
	{
		$connection = $container->resolve('laravel.cache.memcache.connection');

		$key = $container->resolve('laravel.config')->get('cache.key');

		return new Cache\Memcached($connection, $key);
	}),


	'laravel.session.memcached' => array('resolver' => function($container)
	{
		$lifetime = $container->resolve('laravel.config')->get('session.lifetime');

		return new Session\Memcached($container->resolve('laravel.cache.memcached'), $lifetime);
	}),


	'laravel.cache.memcache.connection' => array('singleton' => true, 'resolver' => function($container)
	{
		if ( ! class_exists('Memcache'))
		{
			throw new \Exception('Attempting to use Memcached, but the Memcache PHP extension is not installed on this server.');
		}

		$memcache = new \Memcache;

		foreach ($container->resolve('laravel.config')->get('cache.servers') as $server)
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