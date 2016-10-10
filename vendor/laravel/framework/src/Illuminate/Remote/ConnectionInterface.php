<?php namespace Illuminate\Remote;

use Closure;

interface ConnectionInterface {

	/**
	 * Define a set of commands as a task.
	 *
	 * @param  string  $task
	 * @param  string|array  $commands
	 * @return void
	 */
	public function define($task, $commands);

	/**
	 * Run a task against the connection.
	 *
	 * @param  string  $task
	 * @param  \Closure  $callback
	 * @return void
	 */
	public function task($task, Closure $callback = null);

	/**
	 * Run a set of commands against the connection.
	 *
	 * @param  string|array  $commands
	 * @param  \Closure  $callback
	 * @return void
	 */
	public function run($commands, Closure $callback = null);

	/**
	 * Upload a local file to the server.
	 *
	 * @param  string  $local
	 * @param  string  $remote
	 * @return void
	 */
	public function put($local, $remote);

	/**
	 * Upload a string to to the given file on the server.
	 *
	 * @param  string  $remote
	 * @param  string  $contents
	 * @return void
	 */
	public function putString($remote, $contents);

}
