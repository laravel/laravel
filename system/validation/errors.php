<?php namespace System\Validation;

class Errors {

	/**
	 * All of the error messages.
	 *
	 * @var array
	 */
	public $messages;

	/**
	 * Create a new Errors instance.
	 *
	 * @return void
	 */
	public function __construct($messages = array())
	{
		$this->messages = $messages;
	}

	/**
	 * Add an error message to the collector.
	 *
	 * Duplicate messages will not be added.
	 *
	 * @param  string  $attribute
	 * @param  string  $message
	 * @return void
	 */
	public function add($attribute, $message)
	{
		// Make sure the error message is not duplicated.
		if ( ! array_key_exists($attribute, $this->messages) or ! is_array($this->messages[$attribute]) or ! in_array($message, $this->messages[$attribute]))
		{
			$this->messages[$attribute][] = $message;
		}
	}

	/**
	 * Determine if errors exist for an attribute.
	 *
	 * @param  string  $attribute
	 * @return bool
	 */
	public function has($attribute)
	{
		return $this->first($attribute) !== '';
	}

	/**
	 * Get the first error message for an attribute.
	 *
	 * @param  string  $attribute
	 * @return string
	 */
	public function first($attribute)
	{
		return (count($messages = $this->get($attribute)) > 0) ? $messages[0] : '';
	}

	/**
	 * Get all of the error messages for an attribute.
	 *
	 * If no attribute is specified, all of the error messages will be returned.
	 *
	 * @param  string  $attribute
	 * @param  string  $format
	 * @return array
	 */
	public function get($attribute = null, $format = ':message')
	{
		if (is_null($attribute))
		{
			return $this->all($format);
		}

		return (array_key_exists($attribute, $this->messages)) ? $this->format($this->messages[$attribute], $format) : array();
	}

	/**
	 * Get all of the error messages.
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