<?php namespace Laravel;

class Fluent {

	/**
	 * All of the attributes set on the fluent container.
	 *
	 * @var array
	 */
	public $attributes = array();

	/**
	 * Create a new fluent container instance.
	 *
	 * <code>
	 *		Create a new fluent container with attributes
	 *		$fluent = new Fluent(array('name' => 'Taylor'));
	 * </code>
	 *
	 * @param  array  $attributes
	 * @return void
	 */
	public function __construct($attributes = array())
	{
		foreach ($attributes as $key => $value)
		{
			$this->$key = $value;
		}
	}

	/**
	 * Get an attribute from the fluent container.
	 *
	 * @param  string  $attribute
	 * @param  mixed   $default
	 * @return mixed
	 */
	public function get($attribute, $default = null)
	{
		return array_get($this->attributes, $attribute, $default);
	}

	/**
	 * Handle dynamic calls to the container to set attributes.
	 *
	 * <code>
	 *		// Fluently set the value of a few attributes
	 *		$fluent->name('Taylor')->age(25);
	 *
	 *		// Set the value of an attribute to true (boolean)
	 *		$fluent->nullable()->name('Taylor');
	 * </code>
	 */
	public function __call($method, $parameters)
	{
		$this->$method = (count($parameters) > 0) ? $parameters[0] : true;

		return $this;
	}

	/**
	 * Dynamically retrieve the value of an attribute.
	 */
	public function __get($key)
	{
		if (array_key_exists($key, $this->attributes))
		{
			return $this->attributes[$key];
		}
	}

	/**
	 * Dynamically set the value of an attribute.
	 */
	public function __set($key, $value)
	{
		$this->attributes[$key] = $value;
	}

}