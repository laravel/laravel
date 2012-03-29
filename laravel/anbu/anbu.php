<?php namespace Laravel\Anbu;

use Laravel\View;
use Laravel\File;
use Laravel\Config;
use Laravel\Event;

/**
 * Anbu, the light weight profiler for Laravel.
 *
 * Anbu is intended for inclusion with the Laravel framework.
 *
 * @author 		Dayle Rees <me@daylerees.com>
 * @copyright 	2012 Dayle Rees <me@daylerees.com>
 * @license 	MIT License <http://www.opensource.org/licenses/mit>
 */
class Anbu {

	/**
	 * An array of log entries recorded.
	 *
	 * @var array
	 */
	private static $logs = array();

	/**
	 * Am array of SQL queries executed.
	 *
	 * @var array
	 */
	private static $queries = array();


	/**
	 * Render Anbu, assign view params and echo out the main view.
	 *
	 * @return void
	 */
	public static function render()
	{
		$data = array(
			'anbu_logs'		=> static::$logs,
			'anbu_queries'	=> static::$queries,
			'anbu_css' 		=> File::get(path('sys').'anbu/anbu.css'),
			'anbu_js' 		=> File::get(path('sys').'anbu/anbu.js'),
			'anbu_config' 	=> Config::get('anbu')
		);

		echo View::make('path: '.path('sys').'anbu/template.php', $data)->render();
	}

	/**
	 * Add a log entry to the log entries array.
	 *
	 * @return void
	 */
	public static function log($type, $message)
	{
		static::$logs[] = array($type, $message);
	}

	/**
	 * Add a performed SQL query to Anbu.
	 *
	 * @param 	string 	$sql
	 * @param 	array 	$bindings
	 * @param 	float 	$time
	 * @return 	void
	 */
	public static function sql($sql, $bindings, $time)
	{
		// I used this method to swap in the bindings, its very ugly
		// will be replaced later, hopefully will find something in
		// the core
		foreach ($bindings as $b)
		{
			$count = 1;
			$sql = str_replace('?', '`'.$b.'`', $sql,$count);
		}

		static::$queries[] = array($sql, $time);
	}

	/**
	 * Start Anbu's event listeners.
	 *
	 * @return void
	 */
	public static function register()
	{
		// load the event listeners from a closure in the
		// anbu config file, this allows the user to easily
		// modify them
		$listener = Config::get('anbu.event_listeners');
		$listener();

		// echo anbu on laravel.done if enabled
		if(Config::get('anbu.enable'))
		{
			Event::listen('laravel.done', function() {
				Anbu::render();
			});
		}
	}

}
