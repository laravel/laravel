<?php namespace System\Validation\Rules;

use System\Validation\Rule;

class With_Callback extends Rule {

	/**
	 * The callback.
	 *
	 * @var function
	 */
	public $callback;

	/**
	 * Evaluate the validity of an attribute.
	 *
	 * @param  string  $attribute
	 * @param  array   $attributes
	 * @return void
	 */
	public function check($attribute, $attributes)
	{
		if ( ! array_key_exists($attribute, $attributes))
		{
			return true;
		}

		if ( ! is_callable($this->callback))
		{
			throw new \Exception("A validation callback for the [$attribute] attribute is not callable.");
		}

		return call_user_func($this->callback, $attributes[$attribute]);
	}

	/**
	 * Set the validation callback.
	 *
	 * @param  function  $callback
	 * @return With_Callback
	 */
	public function using($callback)
	{
		$this->callback = $callback;
		return $this;
	}

}