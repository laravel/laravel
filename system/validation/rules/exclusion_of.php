<?php namespace System\Validation\Rules;

use System\Validation\Nullable_Rule;

class Exclusion_Of extends Nullable_Rule {

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
	 * @return bool
	 */
	public function check($attribute, $attributes)
	{
		if ( ! is_null($nullable = parent::check($attribute, $attributes)))
		{
			return $nullable;
		}

		return ! in_array($attributes[$attribute], $this->reserved);
	}	

	/**
	 * Set the reserved values for the attribute
	 *
	 * @param  array         $reserved
	 * @return Exclusion_Of
	 */
	public function from($reserved)
	{
		$this->reserved = $reserved;
		return $this;
	}

}