<?php namespace System\Validation\Rules;

use System\File;
use System\Input;
use System\Validation\Rule;

class Upload_Of extends Rule {

	/**
	 * The acceptable file types.
	 *
	 * @var array
	 */
	public $types = array();

	/**
	 * The maximum file size in bytes.
	 *
	 * @var int
	 */
	public $maximum;

	/**
	 * Evaluate the validity of an attribute.
	 *
	 * @param  string  $attribute
	 * @param  array   $attributes
	 * @return void
	 */
	public function check($attribute, $attributes)
	{
		if ( ! array_key_exists($attribute, Input::file()))
		{
			return true;
		}

		$file = Input::file($attribute);

		if ( ! is_null($this->maximum) and $file['size'] > $this->maximum)
		{
			$this->error = 'file_too_big';
			return false;
		}

		foreach ($this->types as $type)
		{
			if ( ! File::is($type, $file['tmp_name']))
			{
				$this->error = 'file_wrong_type';
				return false;
			}
		}

		return true;
	}

	/**
	 * Set the acceptable file types.
	 *
	 * @return Upload_Of
	 */
	public function is()
	{
		$this->types = func_get_args();
		return $this;
	}

	/**
	 * Set the maximum file size in bytes.
	 *
	 * @param  int  $maximum
	 * @return Upload_Of
	 */
	public function less_than($maximum)
	{
		$this->maximum = $maximum;
		return $this;
	}

}