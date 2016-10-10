<?php namespace Illuminate\Redis;

use Predis\Client;

class Database {

	/**
	 * The host address of the database.
	 *
	 * @var array
	 */
	protected $clients;

	/**
	 * Create a new Redis connection instance.
	 *
	 * @param  array  $servers
	 * @return void
	 */
	public function __construct(array $servers = array())
	{
		if (isset($servers['cluster']) && $servers['cluster'])
		{
			$this->clients = $this->createAggregateClient($servers);
		}
		else
		{
			$this->clients = $this->createSingleClients($servers);
		}
	}

	/**
	 * Create a new aggregate client supporting sharding.
	 *
	 * @param  array  $servers
	 * @return array
	 */
	protected function createAggregateClient(array $servers)
	{
		$servers = array_except($servers, array('cluster'));

		return array('default' => new Client(array_values($servers)));
	}

	/**
	 * Create an array of single connection clients.
	 *
	 * @param  array  $servers
	 * @return array
	 */
	protected function createSingleClients(array $servers)
	{
		$clients = array();

		foreach ($servers as $key => $server)
		{
			$clients[$key] = new Client($server);
		}

		return $clients;
	}

	/**
	 * Get a specific Redis connection instance.
	 *
	 * @param  string  $name
	 * @return \Predis\Connection\SingleConnectionInterface
	 */
	public function connection($name = 'default')
	{
		return $this->clients[$name ?: 'default'];
	}

	/**
	 * Run a command against the Redis database.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function command($method, array $parameters = array())
	{
		return call_user_func_array(array($this->clients['default'], $method), $parameters);
	}

	/**
	 * Dynamically make a Redis command.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		return $this->command($method, $parameters);
	}

}
