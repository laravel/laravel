<?php namespace System\Validation\Rules;

use System\Validation\Rule;

class Exclusion_Of extends Rule {

	/**
	 * The reserved values for the attribute.
	 *
	 * @var string
	 */
	public $reserved;

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

		return ! in_array($attributes[$attribute], $this->reserved);
	}	

	/**
	 * Set the reserved values for the attribute
	 *
	 * @param  array  $reserved
	 * @return Exclusion_Of
	 */
	public function from($reserved)
	{
		$this->reserved = $reserved;
		return $this;
	}

}