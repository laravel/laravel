<?php namespace Laravel;

class Config {

	/**
	 * All of the loaded configuration items.
	 *
	 * @var array
	 */
	protected $config = array();

	/**
	 * Create a new configuration manager instance.
	 *
	 * @param  array  $config
	 * @return void
	 */
	public function __construct($config)
	{
		$this->config = $config;
	}

	/**
	 * Determine if a configuration item or file exists.
	 *
	 * <code>
	 *		// Determine if the "options" configuration file exists
	 *		$options = Config::has('options');
	 *
	 *		// Determine if a specific configuration item exists
	 *		$timezone = Config::has('application.timezone');
	 * </code>
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function has($key)
	{
		return ! is_null($this->get($key));
	}

	/**
	 * Get a configuration item.
	 *
	 * Configuration items are stored in the application/config directory, and provide
	 * general configuration options for a wide range of Laravel facilities.
	 *
	 * The arrays may be accessed using JavaScript style "dot" notation to drill deep
	 * intot he configuration files. For example, asking for "database.connectors.sqlite"
	 * would return the connector closure for SQLite stored in the database configuration
	 * file. If no specific item is specfied, the entire configuration array is returned.
	 *
	 * Like most Laravel "get" functions, a default value may be provided, and it will
	 * be returned if the requested file or item doesn't exist.
	 *
	 * <code>
	 *		// Get the "timezone" option from the application config file
	 *		$timezone = Config::get('application.timezone');
	 *
	 *		// Get an option, but return a default value if it doesn't exist
	 *		$value = Config::get('some.option', 'Default');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  string  $default
	 * @return array
	 */
	public function get($key, $default = null)
	{
		return Arr::get($this->items, $key, $default);
	}

	/**
	 * Set a configuration item.
	 *
	 * Configuration items are stored in the application/config directory, and provide
	 * general configuration options for a wide range of Laravel facilities.
	 *
	 * Like the "get" method, this method uses JavaScript style "dot" notation to access
	 * and manipulate the arrays in the configuration files. Also, like the "get" method,
	 * if no specific item is specified, the entire configuration array will be set.
	 *
	 * <code>
	 *		// Set the "timezone" option in the "application" array
	 *		Config::set('application.timezone', 'America/Chicago');
	 *
	 *		// Set the entire "session" configuration array
	 *		Config::set('session', $array);
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function set($key, $value)
	{
		Arr::set($this->items, $key, $value);
	}

}