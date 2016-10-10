<?php namespace Illuminate\Queue;

use Closure;

class QueueManager {

	/**
	 * The application instance.
	 *
	 * @var \Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * The array of resolved queue connections.
	 *
	 * @var array
	 */
	protected $connections = array();

	/**
	 * Create a new queue manager instance.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 * @return void
	 */
	public function __construct($app)
	{
		$this->app = $app;
	}

	/**
	 * Register an event listener for the failed job event.
	 *
	 * @param  mixed  $callback
	 * @return void
	 */
	public function failing($callback)
	{
		$this->app['events']->listen('illuminate.queue.failed', $callback);
	}

	/**
	 * Determine if the driver is connected.
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public function connected($name = null)
	{
		return isset($this->connections[$name ?: $this->getDefaultDriver()]);
	}

	/**
	 * Resolve a queue connection instance.
	 *
	 * @param  string  $name
	 * @return \Illuminate\Queue\QueueInterface
	 */
	public function connection($name = null)
	{
		$name = $name ?: $this->getDefaultDriver();

		// If the connection has not been resolved yet we will resolve it now as all
		// of the connections are resolved when they are actually needed so we do
		// not make any unnecessary connection to the various queue end-points.
		if ( ! isset($this->connections[$name]))
		{
			$this->connections[$name] = $this->resolve($name);

			$this->connections[$name]->setContainer($this->app);
		}

		return $this->connections[$name];
	}

	/**
	 * Resolve a queue connection.
	 *
	 * @param  string  $name
	 * @return \Illuminate\Queue\QueueInterface
	 */
	protected function resolve($name)
	{
		$config = $this->getConfig($name);

		return $this->getConnector($config['driver'])->connect($config);
	}

	/**
	 * Get the connector for a given driver.
	 *
	 * @param  string  $driver
	 * @return \Illuminate\Queue\Connectors\ConnectorInterface
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function getConnector($driver)
	{
		if (isset($this->connectors[$driver]))
		{
			return call_user_func($this->connectors[$driver]);
		}

		throw new \InvalidArgumentException("No connector for [$driver]");
	}

	/**
	 * Add a queue connection resolver.
	 *
	 * @param  string   $driver
	 * @param  Closure  $resolver
	 * @return void
	 */
	public function extend($driver, Closure $resolver)
	{
		return $this->addConnector($driver, $resolver);
	}

	/**
	 * Add a queue connection resolver.
	 *
	 * @param  string   $driver
	 * @param  Closure  $resolver
	 * @return void
	 */
	public function addConnector($driver, Closure $resolver)
	{
		$this->connectors[$driver] = $resolver;
	}

	/**
	 * Get the queue connection configuration.
	 *
	 * @param  string  $name
	 * @return array
	 */
	protected function getConfig($name)
	{
		return $this->app['config']["queue.connections.{$name}"];
	}

	/**
	 * Get the name of the default queue connection.
	 *
	 * @return string
	 */
	public function getDefaultDriver()
	{
		return $this->app['config']['queue.default'];
	}

	/**
	 * Set the name of the default queue connection.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function setDefaultDriver($name)
	{
		$this->app['config']['queue.default'] = $name;
	}

	/**
	 * Get the full name for the given connection.
	 *
	 * @param  string  $connection
	 * @return string
	 */
	public function getName($connection = null)
	{
		return $connection ?: $this->getDefaultDriver();
	}

	/**
	 * Dynamically pass calls to the default connection.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		$callable = array($this->connection(), $method);

		return call_user_func_array($callable, $parameters);
	}

}
