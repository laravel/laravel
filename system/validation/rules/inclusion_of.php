<?php namespace System\Validation\Rules;

use System\Validation\Nullable_Rule;

class Inclusion_Of extends Nullable_Rule {

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
	 * @return bool
	 */
	public function check($attribute, $attributes)
	{
		if ( ! is_null($nullable = parent::check($attribute, $attributes)))
		{
			return $nullable;
		}

		return in_array($attributes[$attribute], $this->accepted);
	}	

	/**
	 * Set the accepted values for the attribute.
	 *
	 * @param  array         $accepted
	 * @return Inclusion_Of
	 */
	public function in($accepted)
	{
		$this->accepted = $accepted;
		return $this;
	}

}