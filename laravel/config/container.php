<?php namespace Laravel;

return array(

	/*
	|--------------------------------------------------------------------------
	| Core Laravel Components
	|--------------------------------------------------------------------------
	*/

	'laravel.asset' => array('singleton' => true, 'resolver' => function($c)
	{
		return new Asset($c->resolve('laravel.html'));
	}),


	'laravel.auth' => array('singleton' => true, 'resolver' => function($c)
	{
		return new Security\Auth($c->resolve('laravel.config'), $c->resolve('laravel.session'));
	}),


	'laravel.config' => array('singleton' => true, 'resolver' => function($c)
	{
		$paths = array(SYS_CONFIG_PATH, CONFIG_PATH);

		if (isset($_SERVER['LARAVEL_ENV']))
		{
			$paths[] = CONFIG_PATH.$_SERVER['LARAVEL_ENV'].'/';
		}

		return new Config($paths);
	}),


	'laravel.crypter' => array('resolver' => function($c)
	{
		return new Security\Crypter(MCRYPT_RIJNDAEL_256, 'cbc', $c->resolve('laravel.config')->get('application.key'));
	}),


	'laravel.cookie' => array('singleton' => true, 'resolver' => function()
	{
		return new Cookie($_COOKIE);		
	}),


	'laravel.database' => array('singleton' => true, 'resolver' => function($c)
	{
		return new Database\Manager($c->resolve('laravel.config'));
	}),


	'laravel.download' => array('singleton' => true, 'resolver' => function($c)
	{
		return new Download($c->resolve('laravel.file'));		
	}),


	'laravel.file' => array('singleton' => true, 'resolver' => function($c)
	{
		return new File($c->resolve('laravel.config')->get('mimes'));
	}),


	'laravel.form' => array('singleton' => true, 'resolver' => function($c)
	{
		return new Form($c->resolve('laravel.request'), $c->resolve('laravel.html'), $c->resolve('laravel.url'));
	}),


	'laravel.hasher' => array('singleton' => true, 'resolver' => function($c)
	{
		return new Security\Hashing\Bcrypt(8, false);
	}),


	'laravel.html' => array('singleton' => true, 'resolver' => function($c)
	{
		return new HTML($c->resolve('laravel.url'), $c->resolve('laravel.config')->get('application.encoding'));
	}),


	'laravel.input' => array('singleton' => true, 'resolver' => function($c)
	{
		list($file, $cookie, $input, $files) = array(
			$c->resolve('laravel.file'),
			$c->resolve('laravel.cookie'),
			$c->resolve('laravel.input.array'),
			$_FILES,
		);

		return new Input($file, $cookie, $input, $files);
	}),


	'laravel.input.array' => array('singleton' => true, 'resolver' => function($c)
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

		return $input;
	}),


	'laravel.lang' => array('singleton' => true, 'resolver' => function($c)
	{
		require_once SYS_PATH.'lang'.EXT;

		return new Lang_Factory($c->resolve('laravel.config'), array(SYS_LANG_PATH, LANG_PATH));
	}),


	'laravel.loader' => array('singleton' => true, 'resolver' => function($c)
	{
		require_once SYS_PATH.'loader'.EXT;

		$aliases = $c->resolve('laravel.config')->get('aliases');

		return new Loader(array(BASE_PATH, APP_PATH.'models/', APP_PATH), $aliases);
	}),


	'laravel.redirect' => array('singleton' => true, 'resolver' => function($c)
	{
		return new Redirect($c->resolve('laravel.url'));
	}),


	'laravel.request' => array('singleton' => true, 'resolver' => function($c)
	{
		return new Request($c->resolve('laravel.uri')->get(), $_SERVER, $_POST);
	}),


	'laravel.response' => array('singleton' => true, 'resolver' => function($c)
	{
		require_once SYS_PATH.'response'.EXT;

		return new Response_Factory($c->resolve('laravel.view'), $c->resolve('laravel.file'));
	}),


	'laravel.uri' => array('singleton' => true, 'resolver' => function($c)
	{
		return new URI($_SERVER, $c->resolve('laravel.config')->get('application.url'));		
	}),


	'laravel.url' => array('singleton' => true, 'resolver' => function($c)
	{
		list($router, $request, $base, $index) = array(
			$c->resolve('laravel.routing.router'),
			$c->resolve('laravel.request'),
			$c->resolve('laravel.config')->get('application.url'),
			$c->resolve('laravel.config')->get('application.index'),
		);

		return new URL($router, $base, $index, $request->secure());
	}),


	'laravel.validator' => array('singleton' => true, 'resolver' => function($c)
	{
		require_once SYS_PATH.'validation/validator'.EXT;

		return new Validation\Validator_Factory($c->resolve('laravel.lang'));
	}),


	'laravel.view' => array('singleton' => true, 'resolver' => function($c)
	{
		require_once SYS_PATH.'view'.EXT;

		return new View_Factory(new View_Composer(require APP_PATH.'composers'.EXT), VIEW_PATH);
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

	'laravel.cache' => array('singleton' => true, 'resolver' => function($c)
	{
		return new Cache\Manager($c, $c->resolve('laravel.config')->get('cache.driver'));
	}),


	'laravel.cache.apc' => array('resolver' => function($c)
	{
		require_once SYS_PATH.'cache/drivers/apc'.EXT;

		$key = $c->resolve('laravel.config')->get('cache.key');

		return new Cache\Drivers\APC(new Cache\Drivers\APC_Engine, $key);
	}),


	'laravel.cache.file' => array('resolver' => function($c)
	{
		return new Cache\Drivers\File($c->resolve('laravel.file'), CACHE_PATH);
	}),


	'laravel.cache.memcached' => array('resolver' => function($c)
	{
		$key = $c->resolve('laravel.config')->get('cache.key');

		return new Cache\Drivers\Memcached($c->resolve('laravel.cache.memcache.connection'), $key);
	}),


	'laravel.cache.memcache.connection' => array('singleton' => true, 'resolver' => function($c)
	{
		$memcache = new \Memcache;

		foreach ($c->resolve('laravel.config')->get('cache.servers') as $server)
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
		$config = $c->resolve('laravel.config');

		$driver = $c->resolve('laravel.session.'.$config->get('session.driver'));

		return new Session\Manager($driver, $c->resolve('laravel.session.transporter'), $config);
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
		$cookies = $c->resolve('laravel.cookie');

		return new Session\Drivers\Cookie($c->resolve('laravel.crypter'), $cookies);
	}),


	'laravel.session.database' => array('resolver' => function($c)
	{
		return new Session\Drivers\Database($c->resolve('laravel.database')->connection());
	}),


	'laravel.session.file' => array('resolver' => function($c)
	{
		return new Session\Drivers\File($c->resolve('laravel.file'), SESSION_PATH);
	}),


	'laravel.session.memcached' => array('resolver' => function($c)
	{
		return new Session\Drivers\Memcached($c->resolve('laravel.cache.memcached'));
	}),

);