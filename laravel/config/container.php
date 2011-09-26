<?php namespace Laravel;

return array(

	/*
	|--------------------------------------------------------------------------
	| Core Laravel Components
	|--------------------------------------------------------------------------
	*/

	'laravel.auth' => array('singleton' => true, 'resolver' => function($c)
	{
		return new Security\Auth($c->resolve('laravel.session'));
	}),


	'laravel.cookie' => array('singleton' => true, 'resolver' => function($c)
	{
		return new Cookie($_COOKIE);
	}),


	'laravel.crypter' => array('resolver' => function($c)
	{
		return new Security\Crypter(MCRYPT_RIJNDAEL_256, 'cbc', Config::get('application.key'));
	}),


	'laravel.hasher' => array('singleton' => true, 'resolver' => function($c)
	{
		return new Security\Hashing\Bcrypt(8, false);
	}),


	'laravel.input' => array('singleton' => true, 'resolver' => function($c)
	{
		$input = array();

		switch ($c->resolve('laravel.request')->method())
		{
			case 'GET':
				$input = $_GET;
				break;

			case 'POST':
				$input = $_POST;
				break;

			case 'PUT':
			case 'DELETE':
				if ($c->resolve('laravel.request')->spoofed())
				{
					$input = $_POST;
				}
				else
				{
					parse_str(file_get_contents('php://input'), $input);
				}
		}

		unset($input[Request::spoofer]);

		return new Input($input, $_FILES);
	}),


	'laravel.request' => array('singleton' => true, 'resolver' => function($c)
	{
		return new Request($c->resolve('laravel.uri'), $_SERVER, $_POST);		
	}),


	'laravel.uri' => array('singleton' => true, 'resolver' => function($c)
	{
		return new URI($_SERVER);
	}),


	'laravel.view' => array('singleton' => true, 'resolver' => function($c)
	{
		require_once SYS_PATH.'view'.EXT;

		return new View_Factory($c->resolve('laravel.composer'), VIEW_PATH, STORAGE_PATH.'views/');
	}),


	'laravel.composer' => array('singleton' => true, 'resolver' => function($c)
	{
		return new View_Composer(require APP_PATH.'composers'.EXT);
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel Routing Components
	|--------------------------------------------------------------------------
	*/

	'laravel.routing.router' => array('singleton' => true, 'resolver' => function($c)
	{
		return new Routing\Router($c->resolve('laravel.routing.loader'), CONTROLLER_PATH);
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
	| Laravel Caching Components
	|--------------------------------------------------------------------------
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
		return new Cache\Drivers\Memcached($c->resolve('laravel.cache.memcache.connection'), Config::get('cache.key'));
	}),


	'laravel.cache.memcache.connection' => array('singleton' => true, 'resolver' => function($c)
	{
		$memcache = new \Memcache;

		foreach (Config::get('cache.servers') as $server)
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
	*/

	'laravel.session.id' => array('singleton' => true, 'resolver' => function($c)
	{
		return $c->resolve('laravel.cookie')->get('laravel_session');
	}),


	'laravel.session.manager' => array('singleton' => true, 'resolver' => function($c)
	{
		$driver = $c->resolve('laravel.session.'.Config::get('session.driver'));

		return new Session\Manager($driver, $c->resolve('laravel.session.transporter'));
	}),


	'laravel.session.transporter' => array('resolver' => function($c)
	{
		return new Session\Transporters\Cookie($c->resolve('laravel.cookie'));
	}),


	'laravel.session.apc' => array('resolver' => function($c)
	{
		return new Session\Drivers\APC($c->resolve('laravel.cache.apc'));
	}),


	'laravel.session.cookie' => array('resolver' => function($c)
	{
		return new Session\Drivers\Cookie($c->resolve('laravel.crypter'), $c->resolve('laravel.cookie'));
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
		return new Session\Drivers\Memcached($c->resolve('laravel.cache.memcached'));
	}),

);