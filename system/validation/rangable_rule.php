<?php namespace System\Validation;

abstract class Rangable_Rule extends Nullable_Rule {

	/**
	 * The exact size the attribute must be.
	 *
	 * @var int
	 */
	public $size;

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
	 * The "wrong size" error message.
	 *
	 * @var string
	 */
	public $wrong_size;

	/**
	 * The "too big" error message.
	 *
	 * @var string
	 */
	public $too_big;

	/**
	 * The "too small" error message.
	 *
	 * @var string
	 */
	public $too_small;

	/**
	 * Set the exact size the attribute must be.
	 *
	 * @param  int  $size
	 * @return Rangable_Rule
	 */
	public function is($size)
	{
		$this->size = $size;
		return $this;
	}

	/**
	 * Set the minimum and maximum size of the attribute.
	 *
	 * @param  int  $minimum
	 * @param  int  $maximum
	 * @return Rangable_Rule
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
	 * @return Rangable_Rule
	 */
	public function minimum($minimum)
	{
		$this->minimum = $minimum;
		return $this;
	}

	/**
	 * Set the maximum size the attribute.
	 *
	 * @param  int  $maximum
	 * @return Rangable_Rule
	 */
	public function maximum($maximum)
	{
		$this->maximum = $maximum;
		return $this;
	}

	/**
	 * Set the validation error message.
	 *
	 * @param  string   $message
	 * @return Rangable_Rule
	 */
	public function message($message)
	{
		return $this->wrong_size($message)->too_big($message)->too_small($message);
	}

	/**
	 * Set the "wrong size" error message.
	 *
	 * @param  string   $message
	 * @return Rangable_Rule
	 */
	public function wrong_size($message)
	{
		$this->wrong_size = $message;
		return $this;
	}

	/**
	 * Set the "too big" error message.
	 *
	 * @param  string   $message
	 * @return Rangable_Rule
	 */
	public function too_big($message)
	{
		$this->too_big = $message;
		return $this;
	}
	
	/**
	 * Set the "too small" error message.
	 *
	 * @param  string   $message
	 * @return Rangable_Rule
	 */
	public function too_small($message)
	{
		$this->too_small = $message;
		return $this;
	}

}