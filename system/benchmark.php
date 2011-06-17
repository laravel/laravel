<?php namespace System;

class Benchmark {

	/**
	 * Benchmark starting times.
	 *
	 * @var array
	 */
	public static $marks = array();

	/**
	 * Start a benchmark.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public static function start($name)
	{
		static::$marks[$name] = microtime(true);
	}

	/**
	 * Get the elapsed time in milliseconds since starting a benchmark.
	 *
	 * @param  string  $name
	 * @return float
	 */
	public static function check($name)
	{
		if (array_key_exists($name, static::$marks))
		{
			return number_format((microtime(true) - static::$marks[$name]) * 1000, 2);
		}

		throw new \Exception("A Benchmark named [$name] has not been started.");
	}

	/**
	 * Get the total memory usage in megabytes.
	 *
	 * @return float
	 */
	public static function memory()
	{
		return number_format(memory_get_usage() / 1024 / 1024, 2);
	}

}