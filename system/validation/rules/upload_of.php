<?php namespace System\Validation\Rules;

use System\File;
use System\Input;
use System\Validation\Nullable_Rule;

class Upload_Of extends Nullable_Rule {

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
	 * The "wrong type" error message.
	 *
	 * @var string
	 */
	public $wrong_type;

	/**
	 * The "too big" error message.
	 *
	 * @var string
	 */
	public $too_big;

	/**
	 * Evaluate the validity of an attribute.
	 *
	 * @param  string  $attribute
	 * @param  array   $attributes
	 * @return bool
	 */
	public function check($attribute, $attributes)
	{
		// -----------------------------------------------------
		// Check the presence of the upload. If the upload does
		// not exist and the upload is required, a presence_of
		// error will be raised.
		//
		// Otherwise no error will be raised.
		// -----------------------------------------------------
		if ( ! array_key_exists($attribute, Input::file()))
		{
			if ( ! $this->allow_null)
			{
				$this->error = 'presence_of';
			}

			return is_null($this->error);
		}

		// -----------------------------------------------------
		// Uploaded files are stored in the $_FILES array, so
		// we use that array instead of the $attributes.
		// -----------------------------------------------------
		$file = Input::file($attribute);

		if ( ! is_null($this->maximum) and $file['size'] > $this->maximum * 1000)
		{
			$this->error = 'file_too_big';
		}

		// -----------------------------------------------------
		// The File::is method uses the Fileinfo PHP extension
		// to determine the MIME type of the file.
		// -----------------------------------------------------
		foreach ($this->types as $type)
		{
			if (File::is($type, $file['tmp_name']))
			{
				break;				
			}

			$this->error = 'file_wrong_type';
		}

		return is_null($this->error);
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
	 * Require that the uploaded file is an image type.
	 *
	 * @return Upload_Of
	 */
	public function is_image()
	{
		$this->types = array_merge($this->types, array('jpg', 'gif', 'png', 'bmp'))
		return $this;
	}

	/**
	 * Set the maximum file size in kilobytes.
	 *
	 * @param  int        $maximum
	 * @return Upload_Of
	 */
	public function maximum($maximum)
	{
		$this->maximum = $maximum;
		return $this;
	}

	/**
	 * Set the validation error message.
	 *
	 * @param  string     $message
	 * @return Upload_Of
	 */
	public function message($message)
	{
		return $this->wrong_type($message)->too_big($message);
	}

	/**
	 * Set the "wrong type" error message.
	 *
	 * @param  string  $message
	 * @return Upload_Of
	 */
	public function wrong_type($message)
	{
		$this->wrong_type = $message;
		return $this;
	}

	/**
	 * Set the "too big" error message.
	 *
	 * @param  string  $message
	 * @return Upload_Of
	 */
	public function too_big($message)
	{
		$this->too_big = $message;
		return $this;
	}

}