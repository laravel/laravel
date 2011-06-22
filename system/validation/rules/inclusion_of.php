<?php namespace System\Validation\Rules;

use System\Validation\Rule;

class Inclusion_Of extends Rule {

	/**
	 * The accepted values for the attribute.
	 *
	 * @var string
	 */
	public $accepted;

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

		return in_array($attributes[$attribute], $this->accepted);
	}	

	/**
	 * Set the accepted values for the attribute.
	 *
	 * @param  array  $accepted
	 * @return Inclusion_Of
	 */
	public function in($accepted)
	{
		$this->accepted = $accepted;
		return $this;
	}

}