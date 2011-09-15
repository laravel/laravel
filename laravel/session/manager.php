<?php namespace Laravel\Session;

use Laravel\Container;

class Manager {

	/**
	 * The container instance.
	 *
	 * @var Container
	 */
	private $container;

	/**
	 * Create a new session manager instance.
	 *
	 * @param  Container  $container
	 * @return void
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Get the session driver.
	 *
	 * The session driver returned will be the driver specified in the session configuration
	 * file. Only one session driver may be active for a given request, so the driver will
	 * be managed as a singleton.
	 *
	 * @param  string          $driver
	 * @return Session\Driver
	 */
	public function driver($driver)
	{
		if ($this->container->registered('laravel.session.'.$driver))
		{
			return $this->container->resolve('laravel.session.'.$driver);
		}

		throw new \Exception("Session driver [$driver] is not supported.");
	}

}