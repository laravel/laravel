<?php namespace Illuminate\Session;

use Illuminate\Support\Manager;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler;

class SessionManager extends Manager {

	/**
	 * Call a custom driver creator.
	 *
	 * @param  string  $driver
	 * @return mixed
	 */
	protected function callCustomCreator($driver)
	{
		return $this->buildSession(parent::callCustomCreator($driver));
	}

	/**
	 * Create an instance of the "array" session driver.
	 *
	 * @return \Illuminate\Session\Store
	 */
	protected function createArrayDriver()
	{
		return new Store($this->app['config']['session.cookie'], new NullSessionHandler);
	}

	/**
	 * Create an instance of the "cookie" session driver.
	 *
	 * @return \Illuminate\Session\Store
	 */
	protected function createCookieDriver()
	{
		$lifetime = $this->app['config']['session.lifetime'];

		return $this->buildSession(new CookieSessionHandler($this->app['cookie'], $lifetime));
	}

	/**
	 * Create an instance of the file session driver.
	 *
	 * @return \Illuminate\Session\Store
	 */
	protected function createFileDriver()
	{
		return $this->createNativeDriver();
	}

	/**
	 * Create an instance of the file session driver.
	 *
	 * @return \Illuminate\Session\Store
	 */
	protected function createNativeDriver()
	{
		$path = $this->app['config']['session.files'];

		return $this->buildSession(new FileSessionHandler($this->app['files'], $path));
	}

	/**
	 * Create an instance of the database session driver.
	 *
	 * @return \Illuminate\Session\Store
	 */
	protected function createDatabaseDriver()
	{
		$connection = $this->getDatabaseConnection();

		$table = $connection->getTablePrefix().$this->app['config']['session.table'];

		return $this->buildSession(new PdoSessionHandler($connection->getPdo(), $this->getDatabaseOptions($table)));
	}

	/**
	 * Get the database connection for the database driver.
	 *
	 * @return \Illuminate\Database\Connection
	 */
	protected function getDatabaseConnection()
	{
		$connection = $this->app['config']['session.connection'];

		return $this->app['db']->connection($connection);
	}


	/**
	 * Get the database session options.
	 *
	 * @param  string $table
	 * @return array
	 */
	protected function getDatabaseOptions($table)
	{
		return array('db_table' => $table, 'db_id_col' => 'id', 'db_data_col' => 'payload', 'db_time_col' => 'last_activity');
	}

	/**
	 * Create an instance of the APC session driver.
	 *
	 * @return \Illuminate\Session\Store
	 */
	protected function createApcDriver()
	{
		return $this->createCacheBased('apc');
	}

	/**
	 * Create an instance of the Memcached session driver.
	 *
	 * @return \Illuminate\Session\Store
	 */
	protected function createMemcachedDriver()
	{
		return $this->createCacheBased('memcached');
	}

	/**
	 * Create an instance of the Wincache session driver.
	 *
	 * @return \Illuminate\Session\Store
	 */
	protected function createWincacheDriver()
	{
		return $this->createCacheBased('wincache');
	}

	/**
	 * Create an instance of the Redis session driver.
	 *
	 * @return \Illuminate\Session\Store
	 */
	protected function createRedisDriver()
	{
		$handler = $this->createCacheHandler('redis');

		$handler->getCache()->getStore()->setConnection($this->app['config']['session.connection']);

		return $this->buildSession($handler);
	}


	/**
	 * Create an instance of a cache driven driver.
	 *
	 * @param  string  $driver
	 * @return \Illuminate\Session\Store
	 */
	protected function createCacheBased($driver)
	{
		return $this->buildSession($this->createCacheHandler($driver));
	}

	/**
	 * Create the cache based session handler instance.
	 *
	 * @param  string  $driver
	 * @return \Illuminate\Session\CacheBasedSessionHandler
	 */
	protected function createCacheHandler($driver)
	{
		$minutes = $this->app['config']['session.lifetime'];

		return new CacheBasedSessionHandler($this->app['cache']->driver($driver), $minutes);
	}

	/**
	 * Build the session instance.
	 *
	 * @param  \SessionHandlerInterface  $handler
	 * @return \Illuminate\Session\Store
	 */
	protected function buildSession($handler)
	{
		return new Store($this->app['config']['session.cookie'], $handler);
	}

	/**
	 * Get the session configuration.
	 *
	 * @return array
	 */
	public function getSessionConfig()
	{
		return $this->app['config']['session'];
	}

	/**
	 * Get the default session driver name.
	 *
	 * @return string
	 */
	public function getDefaultDriver()
	{
		return $this->app['config']['session.driver'];
	}

	/**
	 * Set the default session driver name.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function setDefaultDriver($name)
	{
		$this->app['config']['session.driver'] = $name;
	}

}
