<?php namespace System;

class Benchmark {

	/**
	 * All of the benchmark starting times.
	 *
	 * @var array
	 */
	public static $marks = array();

	/**
	 * Start a benchmark.
	 *
	 * After starting a benchmark, the elapsed time in milliseconds can be
	 * retrieved using the "check" method.
	 *
	 * <code>
	 * Benchmark::start('name');
	 * </code>
	 *
	 * @param  string  $name
	 * @return void
	 * @see    check
	 */
	public static function start($name)
	{
		static::$marks[$name] = microtime(true);
	}

	/**
	 * Get the elapsed time in milliseconds since starting a benchmark.
	 *
	 * <code>
	 * echo Benchmark::check('name');
	 * </code>
	 *
	 * @param  string  $name
	 * @return float
	 * @see    start
	 */
	public static function check($name)
	{
		if (array_key_exists($name, static::$marks))
		{
			return number_format((microtime(true) - static::$marks[$name]) * 1000, 2);
		}

		return 0.0;
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