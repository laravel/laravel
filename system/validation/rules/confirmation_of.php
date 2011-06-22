<?php namespace System\Validation\Rules;

use System\Input;
use System\Validation\Rule;

class Confirmation_Of extends Rule {

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

		return Input::has($attribute.'_confirmation') and $attributes[$attribute] === Input::get($attribute.'_confirmation');
	}

}