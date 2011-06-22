<?php namespace System\Validation;

class Error_Collector {

	/**
	 * All of the error messages.
	 *
	 * @var array
	 */
	public $messages;

	/**
	 * Create a new Error Collector instance.
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
	 * @param  string  $attribute
	 * @param  string  $message
	 * @return void
	 */
	public function add($attribute, $message)
	{
		$this->messages[$attribute][] = $message;
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
	 * @return array
	 */
	public function get($attribute = null)
	{
		if (is_null($attribute))
		{
			$all = array();

			foreach ($this->messages as $messages)
			{
				$all = array_merge($all, $messages);
			}

			return $all;
		}

		return (array_key_exists($attribute, $this->messages)) ? $this->messages[$attribute] : array();
	}

}