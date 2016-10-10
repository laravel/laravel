<?php namespace Illuminate\Support;

use Closure;

abstract class Manager {

	/**
	 * The application instance.
	 *
	 * @var \Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * The registered custom driver creators.
	 *
	 * @var array
	 */
	protected $customCreators = array();

	/**
	 * The array of created "drivers".
	 *
	 * @var array
	 */
	protected $drivers = array();

	/**
	 * Create a new manager instance.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function __construct($app)
	{
		$this->app = $app;
	}

	/**
	 * Get a driver instance.
	 *
	 * @param  string  $driver
	 * @return mixed
	 */
	public function driver($driver = null)
	{
		$driver = $driver ?: $this->getDefaultDriver();

		// If the given driver has not been created before, we will create the instances
		// here and cache it so we can return it next time very quickly. If there is
		// already a driver created by this name, we'll just return that instance.
		if ( ! isset($this->drivers[$driver]))
		{
			$this->drivers[$driver] = $this->createDriver($driver);
		}

		return $this->drivers[$driver];
	}

	/**
	 * Create a new driver instance.
	 *
	 * @param  string  $driver
	 * @return mixed
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function createDriver($driver)
	{
		$method = 'create'.ucfirst($driver).'Driver';

		// We'll check to see if a creator method exists for the given driver. If not we
		// will check for a custom driver creator, which allows developers to create
		// drivers using their own customized driver creator Closure to create it.
		if (isset($this->customCreators[$driver]))
		{
			return $this->callCustomCreator($driver);
		}
		elseif (method_exists($this, $method))
		{
			return $this->$method();
		}

		throw new \InvalidArgumentException("Driver [$driver] not supported.");
	}

	/**
	 * Call a custom driver creator.
	 *
	 * @param  string  $driver
	 * @return mixed
	 */
	protected function callCustomCreator($driver)
	{
		return $this->customCreators[$driver]($this->app);
	}

	/**
	 * Register a custom driver creator Closure.
	 *
	 * @param  string   $driver
	 * @param  Closure  $callback
	 * @return \Illuminate\Support\Manager|static
	 */
	public function extend($driver, Closure $callback)
	{
		$this->customCreators[$driver] = $callback;

		return $this;
	}

	/**
	 * Get all of the created "drivers".
	 *
	 * @return array
	 */
	public function getDrivers()
	{
		return $this->drivers;
	}

	/**
	 * Dynamically call the default driver instance.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		return call_user_func_array(array($this->driver(), $method), $parameters);
	}

}
