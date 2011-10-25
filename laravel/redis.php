<?php namespace Laravel;

class Redis {

	/**
	 * The name of the Redis connection.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * The configuration array for the Redis connection.
	 *
	 * @var array
	 */
	public $config = array();

	/**
	 * The connection to the Redis database.
	 *
	 * @var resource
	 */
	protected static $connection;

	/**
	 * Create a new Redis connection instance.
	 *
	 * @param  string  $name
	 * @param  array   $config
	 * @return void
	 */
	public function __construct($name, $config)
	{
		$this->name = $name;
		$this->config = $config;
	}

	/**
	 * Create a new Redis connection instance.
	 *
	 * @param  string  $connection
	 * @param  array   $config
	 * @return Redis
	 */
	public static function make($name, $config)
	{
		return new static($name, $config);
	}

	/**
	 * Create a new Redis connection instance.
	 *
	 * The Redis connection is managed as a singleton, so if the connection has
	 * already been established, that same connection instance will be returned
	 * on subsequent requests for the connection.
	 *
	 * @param  string  $connection
	 * @return Redis
	 */
	public static function connection()
	{
		if (is_null(static::$connection))
		{
			static::$connection = static::make($name, Config::get('database.redis'))->connect();
		}

		return static::$connection;
	}

	/**
	 * Connect to the Redis database.
	 *
	 * The Redis instance itself will be returned by the method.
	 *
	 * @return Redis
	 */
	public function connect()
	{
		static::$connection = @fsockopen($this->config['host'], $this->config['port'], $error, $message);		

		if (static::$connection === false)
		{
			throw new \Exception("Error establishing Redis connection [{$this->name}]: {$error} - {$message}");
		}

		return $this;
	}

	/**
	 * Execute a command agaisnt the Redis database.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function run($method, $parameters)
	{
		fwrite(static::$connection, $this->command($method, $parameters));

		$reply = trim(fgets(static::$connection, 512));
	}

	/**
	 * Build the Redis command based from a given method and parameters.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return string
	 */
	protected function command($method, $parameters)
	{
		$command = '*'.(count($parameters) + 1).CRLF.'$'.strlen($method).CRLF.strtoupper($method).CRLF;

		foreach ($parameters as $parameter)
		{
			$command .= '$'.strlen($parameter).CRLF.$parameter.CRLF;
		}

		return $command;
	}

	/**
	 * Dynamically make calls to the Redis database.
	 */
	public function __call($method, $parameters)
	{
		return $this->run($method, $parameters);
	}

	/**
	 * Close the connection to the Redis database.
	 *
	 * @return void
	 */
	public function __destruct()
	{
		fclose(static::$connection);
	}

}