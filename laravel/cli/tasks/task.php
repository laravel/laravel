<?php namespace Laravel\CLI\Tasks;

abstract class Task {

	/**
	 * The CLI options for the task.
	 *
	 * @var array
	 */
	public $options = array();

	/**
	 * Get a CLI option.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public function option($key, $default = null)
	{
		return array_get($this->options, $key, $default);
	}

}