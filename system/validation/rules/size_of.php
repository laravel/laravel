<?php namespace System\Validation\Rules;

use System\Str;
use System\Validation\Rule;

class Size_Of extends Rule {

	/**
	 * The exact size the attribute must be.
	 *
	 * @var int
	 */
	public $length;

	/**
	 * The maximum size of the attribute.
	 *
	 * @var int
	 */
	public $maximum;

	/**
	 * The minimum size of the attribute.
	 *
	 * @var int
	 */
	public $minimum;

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

		if (is_numeric($attributes[$attribute]))
		{
			return $this->check_number($attribute, $attributes);
		}
		else
		{
			return $this->check_string($attribute, $attributes);
		}
	}

	/**
	 * Evaluate the validity of a numeric attribute.
	 *
	 * @param  string  $attribute
	 * @param  array   $attributes
	 * @return void
	 */
	private function check_number($attribute, $attributes)
	{
		if ( ! is_null($this->length) and $attributes[$attribute] !== $this->length)
		{
			return false;
		}

		if ( ! is_null($this->maximum) and $attributes[$attribute] > $this->maximum)
		{
			return false;
		}

		if ( ! is_null($this->minimum and $attributes[$attribute] < $this->minimum))
		{
			return false;
		}

		return true;
	}

	/**
	 * Evaluate the validity of a string attribute.
	 *
	 * @param  string  $attribute
	 * @param  array   $attributes
	 * @return void
	 */
	public function check_string($attribute, $attributes)
	{
		$value = trim((string) $attributes[$attribute]);

		if ( ! is_null($this->length) and Str::length($value) !== $this->length)
		{
			return false;
		}

		if ( ! is_null($this->maximum) and Str::length($value) > $this->maximum)
		{
			return false;
		}

		if ( ! is_null($this->minimum) and Str::length($value) < $this->minimum)
		{
			return false;
		}

		return true;
	}

	/**
	 * Set the exact size the attribute must be.
	 *
	 * @param  int  $length
	 * @return Size_Of
	 */
	public function is($length)
	{
		$this->length = $length;
		return $this;
	}

	/**
	 * Set the minimum and maximum size of the attribute.
	 *
	 * @param  int  $minimum
	 * @param  int  $maximum
	 * @return Size_Of
	 */
	public function between($minimum, $maximum)
	{
		$this->minimum = $minimum;
		$this->maximum = $maximum;

		return $this;
	}

	/**
	 * Set the minimum size the attribute.
	 *
	 * @param  int  $minimum
	 * @return Size_Of
	 */
	public function at_least($minimum)
	{
		$this->minimum = $minimum;
		return $this;
	}

	/**
	 * Set the maximum size the attribute.
	 *
	 * @param  int  $maximum
	 * @return Size_Of
	 */
	public function less_than($maximum)
	{
		$this->maximum = $maximum;
		return $this;
	}

}