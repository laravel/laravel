<?php namespace Laravel;

class Messages {

	/**
	 * All of the registered messages.
	 *
	 * @var array
	 */
	public $messages;

	/**
	 * Create a new Messages instance.
	 *
	 * The Messages class provides a convenient wrapper around an array of strings.
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
	 * @param  string  $key
	 * @param  string  $message
	 * @return void
	 */
	public function add($key, $message)
	{
		if ($this->unique($key, $message)) $this->messages[$key][] = $message;
	}

	/**
	 * Determine if a key and message combination already exists.
	 *
	 * @param  string  $key
	 * @param  string  $message
	 * @return bool
	 */
	protected function unique($key, $message)
	{
		return ! isset($this->messages[$key]) or array_search($message, $this->messages[$key]) === false;
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
	 * <code>
	 *		// Get the first message for the e-mail attribute
	 *		$email = $messages->first('email');
	 *
	 *		// Format the first message for the e-mail attribute
	 *		$email = $messages->first('email', '<p>:message</p>');
	 * </code>
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
	 * <code>
	 *		// Get all of the messages for the e-mail attribute
	 *		$email = $messages->get('email');
	 *
	 *		// Format all of the messages for the e-mail attribute
	 *		$email = $messages->get('email', '<p>:message</p>');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  string  $format
	 * @return array
	 */
	public function get($key = null, $format = ':message')
	{
		if (is_null($key)) return $this->all($format);

		if (array_key_exists($key, $this->messages))
		{
			return $this->format($this->messages[$key], $format);
		}

		return array();
	}

	/**
	 * Get all of the messages for every key.
	 *
	 * <code>
	 *		// Get all of the messages in the collector
	 *		$all = $messages->all();
	 *
	 *		// Format all of the messages in the collector
	 *		$all = $messages->all('<p>:message</p>');
	 * </code>
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
	protected function format($messages, $format)
	{
		$messages = (array) $messages;

		foreach ($messages as $key => &$message)
		{
			$message = str_replace(':message', $message, $format);
		}

		return $messages;
	}

}