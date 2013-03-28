<?php namespace Laravel\Profiling;

use Laravel\View;
use Laravel\File;
use Laravel\Event;
use Laravel\Config;
use Laravel\Request;
use Laravel\Database;

class Profiler {

	/**
	 * An array of the recorded Profiler data.
	 *
	 * @var array
	 */
	protected static $data = array('queries' => array(), 'logs' => array(), 'timers' => array());
	
	/**
	 * Get the rendered contents of the Profiler.
	 *
	 * @param  Response  $response
	 * @return string
	 */
	public static function render($response)
	{
		// We only want to send the profiler toolbar if the request is not an AJAX
		// request, as sending it on AJAX requests could mess up JSON driven API
		// type applications, so we will not send anything in those scenarios.
		if ( ! Request::ajax() and Config::get('application.profiler') )
		{
			static::$data['memory'] = get_file_size(memory_get_usage(true));
			static::$data['memory_peak'] = get_file_size(memory_get_peak_usage(true));
			static::$data['time'] = number_format((microtime(true) - LARAVEL_START) * 1000, 2);
			foreach ( static::$data['timers'] as &$timer)
			{
				$timer['running_time'] = number_format((microtime(true) - $timer['start'] ) * 1000, 2);
			}

			return render('path: '.__DIR__.'/template'.BLADE_EXT, static::$data);
		}
	}

	/**
	 * Allow a callback to be timed.
	 *
	 * @param closure $func
	 * @param string $name
	 * @return void
	 */
	public static function time( $func, $name = 'default_func_timer' )
	{
		// First measure the runtime of the func
		$start = microtime(true);
		$func();
		$end = microtime(true);

		// Check to see if a timer by that name exists
		if (isset(static::$data['timers'][$name]))
		{
			$name = $name.uniqid();
		}
		
		// Push the time into the timers array for display
		static::$data['timers'][$name]['start'] = $start;
		static::$data['timers'][$name]['end'] = $end;
		static::$data['timers'][$name]['time'] = number_format(($end - $start) * 1000, 2);
	}

	/**
	 *  Start, or add a tick to a timer.
	 *
	 * @param string $name
	 * @return void
	 */
	public static function tick($name = 'default_timer', $callback = null)
	{
		$name = trim($name);
		if (empty($name)) $name = 'default_timer';

		// Is this a brand new tick?
		if (isset(static::$data['timers'][$name]))
		{
			$current_timer = static::$data['timers'][$name];
			$ticks = count($current_timer['ticks']);

			// Initialize the new time for the tick
			$new_tick = array();
			$mt = microtime(true);
			$new_tick['raw_time'] = $mt - $current_timer['start'];
			$new_tick['time'] = number_format(($mt - $current_timer['start']) * 1000, 2);

			// Use either the start time or the last tick for the diff
			if ($ticks > 0)
			{
				$last_tick = $current_timer['ticks'][$ticks- 1]['raw_time'];
				$new_tick['diff'] = number_format(($new_tick['raw_time'] - $last_tick) * 1000, 2);
			}
			else
			{
				$new_tick['diff'] = $new_tick['time'];
			}

			// Add the new tick to the stack of them
			static::$data['timers'][$name]['ticks'][] = $new_tick;
		}
		else
		{
			// Initialize a start time on the first tick
			static::$data['timers'][$name]['start'] = microtime(true);
			static::$data['timers'][$name]['ticks'] = array();
		}

		// Run the callback for this tick if it's specified
		if ( ! is_null($callback) and is_callable($callback))
		{
			// After we've ticked, call the callback function
			call_user_func_array($callback, array(
				static::$data['timers'][$name]
			));
		}
	}

	/**
	 * Add a log entry to the log entries array.
	 *
	 * @param  string  $type
	 * @param  string  $message
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
			$binding = Database::escape($binding);
			
			$sql = preg_replace('/\?/', $binding, $sql, 1);
			$sql = htmlspecialchars($sql, ENT_QUOTES, 'UTF-8', false);
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
			echo Profiler::render($response);
		});
	}

}
