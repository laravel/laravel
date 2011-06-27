<?php namespace System\Validation;

use System\Str;

abstract class Nullable_Rule extends Rule {

	/**
	 * Indicates an empty value should be considered valid.
	 *
	 * @var bool
	 */
	public $allow_empty = false;

	/**
	 * Indicates null should be considered valid.
	 *
	 * @var bool
	 */
	public $allow_null = false;

	/**
	 * Evaluate the validity of an attribute.
	 *
	 * If this method returns a value, the child class will return it
	 * as the result of the validation. Otherwise, the child class will
	 * continue validating as normal.
	 *
	 * @param  string  $attribute
	 * @param  array   $attributes
	 * @return mixed
	 */
	public function check($attribute, $attributes)
	{
		// -------------------------------------------------------------
		// If the attribute doesn't exist, the child's validation
		// check will be be halted, and a presence_of error will be
		// raised if null is not allowed.
		// -------------------------------------------------------------
		if ( ! array_key_exists($attribute, $attributes))
		{
			if ( ! $this->allow_null)
			{
				$this->error = 'presence_of';
			}

			return is_null($this->error);
		}

		// -------------------------------------------------------------
		// Make sure the attribute is not an empty string. An error
		// will be raised if the attribute is empty and empty strings
		// are not allowed, halting the child's validation.
		// -------------------------------------------------------------
		elseif (Str::length((string) $attributes[$attribute]) == 0 and ! $this->allow_empty)
		{
			$this->error = 'presence_of';

			return false;
		}
	}

	/**
	 * Allow a empty and null to be considered valid.
	 *
	 * @return Nullable_Rule
	 */
	public function not_required()
	{
		return $this->allow_empty()->allow_null();
	}

	/**
	 * Allow empty to be considered valid.
	 *
	 * @return Nullable_Rule
	 */
	public function allow_empty()
	{
		$this->allow_empty = true;
		return $this;
	}

	/**
	 * Allow null to be considered valid.
	 *
	 * @return Nullable_Rule
	 */
	public function allow_null()
	{
		$this->allow_null = true;
		return $this;
	}

}