<?php namespace System\Validation\Rules;

use System\Validation\Nullable_Rule;

class Format_Of extends Nullable_Rule {

	/**
	 * The regular expression that will be used to validate the attribute.
	 *
	 * @var string
	 */
	public $expression;

	/**
	 * Evaluate the validity of an attribute.
	 *
	 * @param  string  $attribute
	 * @param  array   $attributes
	 * @return bool
	 */
	public function check($attribute, $attributes)
	{
		if ( ! is_null($nullable = parent::check($attribute, $attributes)))
		{
			return $nullable;
		}

		return preg_match($this->expression, $attributes[$attribute]);
	}	

	/**
	 * Set the regular expression.
	 *
	 * @param  string     $expression
	 * @return Format_Of
	 */
	public function using($expression)
	{
		$this->expression = $expression;
		return $this;
	}

}