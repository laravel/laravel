<?php namespace System\Validation\Rules;

use System\Validation\Rule;

class Format_Of extends Rule {

	/**
	 * The regular expression that will be used to evaluate the attribute.
	 *
	 * @var string
	 */
	public $expression;

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

		return preg_match($this->expression, $attributes[$attribute]);
	}	

	/**
	 * Set the regular expression.
	 *
	 * @param  string  $expression
	 * @return Format_Of
	 */
	public function with($expression)
	{
		$this->expression = $expression;
		return $this;
	}

}