<?php namespace Laravel\Validation;

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
	 * The Messages class provides a convenient wrapper around an array of generic messages.
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
	 * <code>
	 *		// Add an error message for the "email" key
	 *		$messages->add('email', 'The e-mail address is invalid.');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  string  $message
	 * @return void
	 */
	public function add($key, $message)
	{
		if ( ! isset($this->messages[$key]) or array_search($message, $this->messages[$key]) === false)
		{
			$this->messages[$key][] = $message;
		}
	}

	/**
	 * Determine if messages exist for a given key.
	 *
	 * <code>
	 *		// Determine if there are any messages for the "email" key
	 *		$has = $messages->has('email');
	 * </code>
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
	 * Optionally, a format may be specified for the returned message.
	 *
	 * <code>
	 *		// Get the first message for the "email" key
	 *		$message = $messages->first('email');
	 *
	 *		// Get the first message for the "email" key wrapped in <p> tags
	 *		$message = $messages->first('email', '<p>:message</p>');
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
	 *		// Get all of the messages for the "email" key
	 *		$message = $messages->get('email');
	 *
	 *		// Get all of the messages for the "email" key wrapped in <p> tags
	 *		$message = $messages->get('email', '<p>:message</p>');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  string  $format
	 * @return array
	 */
	public function get($key = null, $format = ':message')
	{
		if (is_null($key)) return $this->all($format);

		return (array_key_exists($key, $this->messages)) ? $this->format($this->messages[$key], $format) : array();
	}

	/**
	 * Get all of the messages for every key.
	 *
	 * <code>
	 *		// Get all of the messages for every key
	 *		$message = $messages->all();
	 *
	 *		// Get all of the messages for every key wrapped in <p> tags
	 *		$message = $messages->all('<p>:message</p>');
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
	private function format($messages, $format)
	{
		foreach ($messages as $key => &$message)
		{
			$message = str_replace(':message', $message, $format);
		}

		return $messages;
	}

}