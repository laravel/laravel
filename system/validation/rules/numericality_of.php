<?php namespace System\Validation\Rules;

use System\Validation\Rangable_Rule;

class Numericality_Of extends Rangable_Rule {

	/**
	 * Indicates that the attribute must be an integer.
	 *
	 * @var bool
	 */
	public $only_integer = false;

	/**
	 * The "not valid" error message.
	 *
	 * @var string
	 */
	public $not_valid;

	/**
	 * The "not integer" error message.
	 *
	 * @var string
	 */
	public $not_integer;

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
		// Validate the attribute is a number.
		// ---------------------------------------------------------
		if ( ! is_numeric($attributes[$attribute]))
		{
			$this->error = 'number_not_valid';
		}
		// ---------------------------------------------------------
		// Validate the attribute is an integer.
		// ---------------------------------------------------------
		elseif ($this->only_integer and filter_var($attributes[$attribute], FILTER_VALIDATE_INT) === false)
		{
			$this->error = 'number_not_integer';
		}
		// ---------------------------------------------------------
		// Validate the exact size of the attribute.
		// ---------------------------------------------------------
		elseif ( ! is_null($this->size) and $attributes[$attribute] != $this->size)
		{
			$this->error = 'number_wrong_size';
		}
		// ---------------------------------------------------------
		// Validate the maximum size of the attribute.
		// ---------------------------------------------------------
		elseif ( ! is_null($this->maximum) and $attributes[$attribute] > $this->maximum)
		{
			$this->error = 'number_too_big';
		}
		// ---------------------------------------------------------
		// Validate the minimum size of the attribute.
		// ---------------------------------------------------------
		elseif ( ! is_null($this->minimum) and $attributes[$attribute] < $this->minimum)
		{
			$this->error = 'number_too_small';
		}

		return is_null($this->error);
	}

	/**
	 * Specify that the attribute must be an integer.
	 *
	 * @return Numericality_Of
	 */
	public function only_integer()
	{
		$this->only_integer = true;
		return $this;
	}

	/**
	 * Set the "not valid" error message.
	 *
	 * @param  string           $message
	 * @return Numericality_Of
	 */
	public function not_valid($message)
	{
		$this->not_valid = $message;
		return $this;
	}

	/**
	 * Set the "not integer" error message.
	 *
	 * @param  string           $message
	 * @return Numericality_Of
	 */
	public function not_integer($message)
	{
		$this->not_integer = $message;
		return $this;
	}

}