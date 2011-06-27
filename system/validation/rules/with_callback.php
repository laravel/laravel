<?php namespace System\Validation\Rules;

use System\Validation\Nullable_Rule;

class With_Callback extends Nullable_Rule {

	/**
	 * The callback that will be used to validate the attribute.
	 *
	 * @var function
	 */
	public $callback;

	/**
	 * Evaluate the validity of an attribute.
	 *
	 * @param  string  $attribute
	 * @param  array   $attributes
	 * @return bool
	 */
	public function check($attribute, $attributes)
	{
		if ( ! is_callable($this->callback))
		{
			throw new \Exception("The validation callback for the [$attribute] attribute is not callable.");
		}

		if ( ! is_null($nullable = parent::check($attribute, $attributes)))
		{
			return $nullable;
		}

		return call_user_func($this->callback, $attributes[$attribute]);
	}

	/**
	 * Set the validation callback.
	 *
	 * @param  function       $callback
	 * @return With_Callback
	 */
	public function using($callback)
	{
		$this->callback = $callback;
		return $this;
	}

}