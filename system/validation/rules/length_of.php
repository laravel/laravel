<?php namespace System\Validation\Rules;

use System\Str;
use System\Validation\Rangable_Rule;

class Length_Of extends Rangable_Rule {

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

		$value = trim((string) $attributes[$attribute]);

		// ---------------------------------------------------------
		// Validate the exact length of the attribute.
		// ---------------------------------------------------------
		if ( ! is_null($this->size) and Str::length($value) !== $this->size)
		{
			$this->error = 'string_wrong_size';
		}
		// ---------------------------------------------------------
		// Validate the maximum length of the attribute.
		// ---------------------------------------------------------
		elseif ( ! is_null($this->maximum) and Str::length($value) > $this->maximum)
		{
			$this->error = 'string_too_big';
		}
		// ---------------------------------------------------------
		// Validate the minimum length of the attribute.
		// ---------------------------------------------------------
		elseif ( ! is_null($this->minimum) and Str::length($value) < $this->minimum)
		{
			$this->error = 'string_too_small';
		}

		return is_null($this->error);
	}

}