<?php namespace System;

class Messages {

	/**
	 * All of the messages.
	 *
	 * @var array
	 */
	public $messages;

	/**
	 * Create a new Messages instance.
	 *
	 * @return void
	 */
	public function __construct($messages = array())
	{
		$this->messages = $messages;
	}

	/**
	 * Add a message to the collector.
	 *
	 * Duplicate messages will not be added.
	 *
	 * @param  string  $key
	 * @param  string  $message
	 * @return void
	 */
	public function add($key, $message)
	{
		// Make sure the message is not duplicated.
		if ( ! array_key_exists($key, $this->messages) or ! is_array($this->messages[$key]) or ! in_array($message, $this->messages[$key]))
		{
			$this->messages[$key][] = $message;
		}
	}

	/**
	 * Determine if messages exist for a given key.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function has($key)
	{
		return $this->first($key) !== '';
	}

	/**
	 * Get the first message for a given key.
	 *
	 * @param  string  $key
	 * @param  string  $format
	 * @return string
	 */
	public function first($key, $format = ':message')
	{
		return (count($messages = $this->get($key, $format)) > 0) ? $messages[0] : '';
	}

	/**
	 * Get all of the messages for a key.
	 *
	 * If no key is specified, all of the messages will be returned.
	 *
	 * @param  string  $key
	 * @param  string  $format
	 * @return array
	 */
	public function get($key = null, $format = ':message')
	{
		if (is_null($key))
		{
			return $this->all($format);
		}

		return (array_key_exists($key, $this->messages)) ? $this->format($this->messages[$key], $format) : array();
	}

	/**
	 * Get all of the messages.
	 *
	 * @param  string  $format
	 * @return array
	 */
	public function all($format = ':message')
	{
		$all = array();

		foreach ($this->messages as $messages)
		{
			$all = array_merge($all, $this->format($messages, $format));
		}

		return $all;
	}

	/**
	 * Format an array of messages.
	 *
	 * @param  array   $messages
	 * @param  string  $format
	 * @return array
	 */
	private function format($messages, $format)
	{
		array_walk($messages, function(&$message, $key) use ($format) { $message = str_replace(':message', $message, $format); });

		return $messages;
	}

}