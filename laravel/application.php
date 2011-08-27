<?php namespace Laravel;

class Application {

	/**
	 * The active request instance.
	 *
	 * @var Request
	 */
	public $request;

	/**
	 * The application input manager.
	 *
	 * @var Input
	 */
	public $input;

	/**
	 * The application configuration manager.
	 *
	 * @var Config
	 */
	public $config;

	/**
	 * The application session driver.
	 *
	 * @var Session\Driver
	 */
	public $session;

	/**
	 * The application cache manager.
	 *
	 * @var Cache\Driver
	 */
	public $cache;

	/**
	 * The application database manager.
	 */
	public $database;

	/**
	 * The application auto-loader.
	 *
	 * @var Loader
	 */
	public $loader;

	/**
	 * The application IoC container.
	 *
	 * @var Container
	 */
	public $container;

	/**
	 * Magic Method for resolving core classes out of the IoC container.
	 */
	public function __get($key)
	{
		if ($this->container->registered('laravel.'.$key))
		{
			return $this->container->resolve('laravel.'.$key);
		}
		elseif ($this->container->registered($key))
		{
			return $this->container->resolve($key);
		}

		throw new \Exception("Attempting to access undefined property [$key] on application instance.");
	}

}