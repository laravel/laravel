<?php namespace System\Validation\Rules;

use System\Validation\Nullable_Rule;

class Presence_Of extends Nullable_Rule {

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

		// ---------------------------------------------------------
		// The Nullable_Rule check method essentially is a check for
		// the presence of an attribute, so there is no further
		// checking that needs to be done.
		// ---------------------------------------------------------
		return true;
	}

}