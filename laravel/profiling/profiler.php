<?php namespace Laravel\Profiling;

use Laravel\View;
use Laravel\File;
use Laravel\Event;
use Laravel\Config;
use Laravel\Request;

class Profiler {

	/**
	 * An array of the recorded Profiler data.
	 *
	 * @var array
	 */
	protected static $data = array('queries' => array(), 'logs' => array());

	/**
	 * Get the rendered contents of the Profiler.
	 *
	 * @return string
	 */
	public static function render()
	{
		if ( ! Request::ajax())
		{
			return render('path: '.__DIR__.'/template'.BLADE_EXT, static::$data);
		}
	}

	/**
	 * Add a log entry to the log entries array.
	 *
	 * @return void
	 */
	public static function log($type, $message)
	{
		static::$data['logs'][] = array($type, $message);
	}

	/**
	 * Add a performed SQL query to the Profiler.
	 *
	 * @param 	string 	$sql
	 * @param 	array 	$bindings
	 * @param 	float 	$time
	 * @return 	void
	 */
	public static function query($sql, $bindings, $time)
	{
		foreach ($bindings as $binding)
		{
			$sql = preg_replace('/\?/', $binding, $sql, 1);
		}

		static::$data['queries'][] = array($sql, $time);
	}

	/**
	 * Attach the Profiler's event listeners.
	 *
	 * @return void
	 */
	public static function attach()
	{
		// First we'll attach to the query and log events. These allow us to catch
		// all of the SQL queries and log messages that come through Laravel,
		// and we will pass them onto the Profiler for simple storage.
		Event::listen('laravel.log', function($type, $message)
		{
			Profiler::log($type, $message);
		});

		Event::listen('laravel.query', function($sql, $bindings, $time)
		{
			Profiler::query($sql, $bindings, $time);			
		});

		// We'll attach the profiler to the "done" event so that we can easily
		// attach the profiler output to the end of the output sent to the
		// browser. This will display the profiler's nice toolbar.
		Event::listen('laravel.done', function($response)
		{
			echo Profiler::render();
		});
	}

}
