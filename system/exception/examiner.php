<?php namespace System\Exception;

use System\File;

class Examiner {

	/**
	 * The exception being examined.
	 *
	 * @var Exception
	 */
	public $exception;

	/**
	 * Human-readable error levels and descriptions.
	 *
	 * @var array
	 */
	private $levels = array(
		0                  => 'Error',
		E_ERROR            => 'Error',
		E_WARNING          => 'Warning',
		E_PARSE            => 'Parsing Error',
		E_NOTICE           => 'Notice',
		E_CORE_ERROR       => 'Core Error',
		E_CORE_WARNING     => 'Core Warning',
		E_COMPILE_ERROR    => 'Compile Error',
		E_COMPILE_WARNING  => 'Compile Warning',
		E_USER_ERROR       => 'User Error',
		E_USER_WARNING     => 'User Warning',
		E_USER_NOTICE      => 'User Notice',
		E_STRICT           => 'Runtime Notice'
	);

	/**
	 * Create a new exception examiner instance.
	 *
	 * @param  Exception  $e
	 * @return void
	 */
	public function __construct($e)
	{
		$this->exception = $e;
	}

	/**
	 * Get a human-readable version of the exception error code.
	 *
	 * @return string
	 */
	public function severity()
	{
		if (array_key_exists($this->exception->getCode(), $this->levels))
		{
			return $this->levels[$this->exception->getCode()];
		}

		return $this->exception->getCode();
	}

	/**
	 * Get the exception error message formatted for use by Laravel.
	 *
	 * The exception file paths will be shortened, and the file name and line number
	 * will be added to the exception message.
	 *
	 * @return string
	 */
	public function message()
	{
		$file = str_replace(array(APP_PATH, SYS_PATH), array('APP_PATH/', 'SYS_PATH/'), $this->exception->getFile());

		return rtrim($this->exception->getMessage(), '.').' in '.$file.' on line '.$this->exception->getLine().'.';
	}

	/**
	 * Get the code surrounding the line where the exception occurred.
	 *
	 * @return array 
	 */
	public function context()
	{
		return File::snapshot($this->exception->getFile(), $this->exception->getLine());
	}

	/**
	 * Magic Method to pass function calls to the exception.
	 */
	public function __call($method, $parameters)
	{
		return call_user_func_array(array($this->exception, $method), $parameters);
	}

}