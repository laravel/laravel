<?php namespace Illuminate\Database;

use Illuminate\Database\Connectors\ConnectionFactory;

class DatabaseManager implements ConnectionResolverInterface {

	/**
	 * The application instance.
	 *
	 * @var \Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * The database connection factory instance.
	 *
	 * @var \Illuminate\Database\Connectors\ConnectionFactory
	 */
	protected $factory;

	/**
	 * The active connection instances.
	 *
	 * @var array
	 */
	protected $connections = array();

	/**
	 * The custom connection resolvers.
	 *
	 * @var array
	 */
	protected $extensions = array();

	/**
	 * Create a new database manager instance.
	 *
	 * @param  \Illuminate\Foundation\Application  $app
	 * @param  \Illuminate\Database\Connectors\ConnectionFactory  $factory
	 * @return void
	 */
	public function __construct($app, ConnectionFactory $factory)
	{
		$this->app = $app;
		$this->factory = $factory;
	}

	/**
	 * Get a database connection instance.
	 *
	 * @param  string  $name
	 * @return \Illuminate\Database\Connection
	 */
	public function connection($name = null)
	{
		$name = $name ?: $this->getDefaultConnection();

		// If we haven't created this connection, we'll create it based on the config
		// provided in the application. Once we've created the connections we will
		// set the "fetch mode" for PDO which determines the query return types.
		if ( ! isset($this->connections[$name]))
		{
			$connection = $this->makeConnection($name);

			$this->connections[$name] = $this->prepare($connection);
		}

		return $this->connections[$name];
	}

	/**
	 * Reconnect to the given database.
	 *
	 * @param  string  $name
	 * @return \Illuminate\Database\Connection
	 */
	public function reconnect($name = null)
	{
		$name = $name ?: $this->getDefaultConnection();

		$this->disconnect($name);

		return $this->connection($name);
	}

	/**
	 * Disconnect from the given database.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function disconnect($name = null)
	{
		$name = $name ?: $this->getDefaultConnection();

		unset($this->connections[$name]);
	}

	/**
	 * Make the database connection instance.
	 *
	 * @param  string  $name
	 * @return \Illuminate\Database\Connection
	 */
	protected function makeConnection($name)
	{
		$config = $this->getConfig($name);

		// First we will check by the connection name to see if an extension has been
		// registered specifically for that connection. If it has we will call the
		// Closure and pass it the config allowing it to resolve the connection.
		if (isset($this->extensions[$name]))
		{
			return call_user_func($this->extensions[$name], $config, $name);
		}

		$driver = $config['driver'];

		// Next we will check to see if an extension has been registered for a driver
		// and will call the Closure if so, which allows us to have a more generic
		// resolver for the drivers themselves which applies to all connections.
		if (isset($this->extensions[$driver]))
		{
			return call_user_func($this->extensions[$driver], $config, $name);
		}

		return $this->factory->make($config, $name);
	}

	/**
	 * Prepare the database connection instance.
	 *
	 * @param  \Illuminate\Database\Connection  $connection
	 * @return \Illuminate\Database\Connection
	 */
	protected function prepare(Connection $connection)
	{
		$connection->setFetchMode($this->app['config']['database.fetch']);

		if ($this->app->bound('events'))
		{
			$connection->setEventDispatcher($this->app['events']);
		}

		// The database connection can also utilize a cache manager instance when cache
		// functionality is used on queries, which provides an expressive interface
		// to caching both fluent queries and Eloquent queries that are executed.
		$app = $this->app;

		$connection->setCacheManager(function() use ($app)
		{
			return $app['cache'];
		});

		// We will setup a Closure to resolve the paginator instance on the connection
		// since the Paginator isn't used on every request and needs quite a few of
		// our dependencies. It'll be more efficient to lazily resolve instances.
		$connection->setPaginator(function() use ($app)
		{
			return $app['paginator'];
		});

		return $connection;
	}

	/**
	 * Get the configuration for a connection.
	 *
	 * @param  string  $name
	 * @return array
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function getConfig($name)
	{
		$name = $name ?: $this->getDefaultConnection();

		// To get the database connection configuration, we will just pull each of the
		// connection configurations and get the configurations for the given name.
		// If the configuration doesn't exist, we'll throw an exception and bail.
		$connections = $this->app['config']['database.connections'];

		if (is_null($config = array_get($connections, $name)))
		{
			throw new \InvalidArgumentException("Database [$name] not configured.");
		}

		return $config;
	}

	/**
	 * Get the default connection name.
	 *
	 * @return string
	 */
	public function getDefaultConnection()
	{
		return $this->app['config']['database.default'];
	}

	/**
	 * Set the default connection name.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function setDefaultConnection($name)
	{
		$this->app['config']['database.default'] = $name;
	}

	/**
	 * Register an extension connection resolver.
	 *
	 * @param  string    $name
	 * @param  callable  $resolver
	 * @return void
	 */
	public function extend($name, $resolver)
	{
		$this->extensions[$name] = $resolver;
	}

	/**
	 * Return all of the created connections.
	 *
	 * @return array
	 */
	public function getConnections()
	{
		return $this->connections;
	}

	/**
	 * Dynamically pass methods to the default connection.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		return call_user_func_array(array($this->connection(), $method), $parameters);
	}

}
