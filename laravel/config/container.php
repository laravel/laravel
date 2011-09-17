<?php namespace Laravel;

return array(

	/*
	|--------------------------------------------------------------------------
	| Laravel Components
	|--------------------------------------------------------------------------
	*/

	'laravel.asset' => array('singleton' => true, 'resolver' => function($container)
	{
		return new Asset($container->resolve('laravel.html'));
	}),


	'laravel.auth' => array('singleton' => true, 'resolver' => function($container)
	{
		return new Security\Auth($container->resolve('laravel.config'), $container->resolve('laravel.session'));
	}),


	'laravel.config' => array('singleton' => true, 'resolver' => function($container)
	{
		$paths = array(SYS_CONFIG_PATH, CONFIG_PATH);

		if (isset($_SERVER['LARAVEL_ENV']))
		{
			$paths[] = CONFIG_PATH.$_SERVER['LARAVEL_ENV'].'/';
		}

		return new Config($paths);
	}),


	'laravel.crypter' => array('resolver' => function($container)
	{
		$key = $container->resolve('laravel.config')->get('application.key');

		return new Security\Crypter(MCRYPT_RIJNDAEL_256, 'cbc', $key);
	}),


	'laravel.cookie' => array('singleton' => true, 'resolver' => function()
	{
		return new Cookie($_COOKIE);		
	}),


	'laravel.database' => array('singleton' => true, 'resolver' => function($container)
	{
		return new Database\Manager($container->resolve('laravel.config')->get('database'));
	}),


	'laravel.download' => array('singleton' => true, 'resolver' => function($container)
	{
		return new Download($container->resolve('laravel.file'));		
	}),


	'laravel.file' => array('singleton' => true, 'resolver' => function($container)
	{
		return new File($container->resolve('laravel.config')->get('mimes'));
	}),


	'laravel.form' => array('singleton' => true, 'resolver' => function($container)
	{
		list($request, $html, $url) = array(
			$container->resolve('laravel.request'),
			$container->resolve('laravel.html'),
			$container->resolve('laravel.url'),
		);

		return new Form($request, $html, $url);
	}),


	'laravel.hasher' => array('singleton' => true, 'resolver' => function($container)
	{
		return new Security\Hashing\Bcrypt(8);
	}),


	'laravel.html' => array('singleton' => true, 'resolver' => function($container)
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

		unset($input['_REQUEST_METHOD_']);

		return new Input($container->resolve('laravel.file'), $container->resolve('laravel.cookie'), $input, $_FILES);
	}),


	'laravel.lang' => array('singleton' => true, 'resolver' => function($container)
	{
		require_once SYS_PATH.'lang'.EXT;

		return new Lang_Factory($container->resolve('laravel.config'), array(SYS_LANG_PATH, LANG_PATH));
	}),


	'laravel.loader' => array('singleton' => true, 'resolver' => function($container)
	{
		require_once SYS_PATH.'loader'.EXT;

		$aliases = $container->resolve('laravel.config')->get('aliases');

		return new Loader(array(BASE_PATH, APP_PATH.'models/', APP_PATH.'libraries/'), $aliases);
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

		return new Response_Factory($container->resolve('laravel.view'), $container->resolve('laravel.file'));
	}),


	'laravel.routing.router' => array('singleton' => true, 'resolver' => function($container)
	{
		return new Routing\Router(require APP_PATH.'routes'.EXT, CONTROLLER_PATH);
	}),


	'laravel.routing.caller' => array('resolver' => function($container)
	{
		return new Routing\Caller($container, require APP_PATH.'filters'.EXT, CONTROLLER_PATH);
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


	'laravel.url' => array('singleton' => true, 'resolver' => function($container)
	{
		list($request, $base, $index) = array(
			$container->resolve('laravel.request'),
			$container->resolve('laravel.config')->get('application.url'),
			$container->resolve('laravel.config')->get('application.index'),
		);

		return new URL($container->resolve('laravel.routing.router'), $base, $index, $request->secure());
	}),


	'laravel.validator' => array('singleton' => true, 'resolver' => function($container)
	{
		require_once SYS_PATH.'validation/validator'.EXT;

		return new Validation\Validator_Factory($container->resolve('laravel.lang'));
	}),


	'laravel.view' => array('singleton' => true, 'resolver' => function($container)
	{
		require_once SYS_PATH.'view'.EXT;

		return new View_Factory($container->resolve('laravel.view.composer'), VIEW_PATH);
	}),


	'laravel.view.composer' => array('resolver' => function($container)
	{
		return new View_Composer(require APP_PATH.'composers'.EXT);
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel Cookie Session Components
	|--------------------------------------------------------------------------
	*/

	'laravel.session.cookie' => array('resolver' => function($container)
	{
		$cookies = $container->resolve('laravel.cookie');

		$config = $container->resolve('laravel.config')->get('session');

		return new Session\Drivers\Cookie($container->resolve('laravel.crypter'), $cookies);
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel Database Session Components
	|--------------------------------------------------------------------------
	*/

	'laravel.session.database' => array('resolver' => function($container)
	{
		$table = $container->resolve('laravel.config')->get('session.table');

		return new Session\Drivers\Database($container->resolve('laravel.database')->connection());
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
		return new Cache\Drivers\File($container->resolve('laravel.file'), CACHE_PATH);
	}),


	'laravel.session.file' => array('resolver' => function($container)
	{
		return new Session\Drivers\File($container->resolve('laravel.file'), SESSION_PATH);
	}),

	/*
	|--------------------------------------------------------------------------
	| Laravel APC Cache & Session Components
	|--------------------------------------------------------------------------
	*/

	'laravel.cache.apc' => array('resolver' => function($container)
	{
		return new Cache\Drivers\APC(new Proxy, $container->resolve('laravel.config')->get('cache.key'));
	}),


	'laravel.session.id' => array('singleton' => true, 'resolver' => function($container)
	{
		return $container->resolve('laravel.cookie')->get('laravel_session');
	}),


	'laravel.session.apc' => array('resolver' => function($container)
	{
		$lifetime = $container->resolve('laravel.config')->get('session.lifetime');

		return new Session\Drivers\APC($container->resolve('laravel.cache.apc'));
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

		return new Cache\Drivers\Memcached($connection, $key);
	}),


	'laravel.session.memcached' => array('resolver' => function($container)
	{
		$lifetime = $container->resolve('laravel.config')->get('session.lifetime');

		return new Session\Drivers\Memcached($container->resolve('laravel.cache.memcached'));
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