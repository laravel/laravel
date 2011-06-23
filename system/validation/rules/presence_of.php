<?php namespace System\Validation\Rules;

use System\Validation\Rule;

class Presence_Of extends Rule {

	/**
	 * Indicates an empty string should be considered present.
	 *
	 * @var bool
	 */
	public $allow_empty = false;

	/**
	 * Indicates null should be considered present.
	 *
	 * @var bool
	 */
	public $allow_null = false;

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
			return false;
		}

		if (is_null($attributes[$attribute]) and ! $this->allow_null)
		{
			return false;
		}

		if (trim((string) $attributes[$attribute]) === '' and ! $this->allow_empty)
		{
			return false;
		}

		return true;
	}

	/**
	 * Allow an empty string to be considered present.
	 *
	 * @return Presence_Of
	 */
	public function allow_empty()
	{
		$this->allow_empty = true;
		return $this;
	}

	/**
	 * Allow a null to be considered present.
	 *
	 * @return Presence_Of
	 */
	public function allow_null()
	{
		$this->allow_null = true;
		return $this;
	}

}