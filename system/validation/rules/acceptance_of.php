<?php namespace System\Validation\Rules;

use System\Input;
use System\Validation\Rule;

class Acceptance_Of extends Rule {

	/**
	 * The value is that is considered accepted.
	 *
	 * @var string
	 */
	public $accepts = '1';

	/**
	 * Evaluate the validity of an attribute.
	 *
	 * @param  string  $attribute
	 * @param  array   $attributes
	 * @return bool
	 */
	public function check($attribute, $attributes)
	{
		return Input::has($attribute) and (string) Input::get($attribute) === $this->accepts;
	}	

	/**
	 * Set the accepted value.
	 *
	 * @param  string         $value
	 * @return Acceptance_Of
	 */
	public function accepts($value)
	{
		$this->accepts = $value;
		return $this;
	}

}