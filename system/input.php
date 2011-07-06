<?php namespace System;

class Input {

	/**
	 * The input data for the request.
	 *
	 * @var array
	 */
	public static $input;

	/**
	 * Determine if the input data contains an item.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public static function has($key)
	{
		return ( ! is_null(static::get($key)) and trim((string) static::get($key)) !== '');
	}

	/**
	 * Get an item from the input data.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return string
	 */
	public static function get($key = null, $default = null)
	{
		if (is_null(static::$input))
		{
			static::hydrate();
		}

		return (array_key_exists($key, static::$input)) ? static::$input[$key] : $default;
	}

	/**
	 * Determine if the old input data contains an item.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public static function had($key)
	{
		return ( ! is_null(static::old($key)) and trim((string) static::old($key)) !== '');
	}

	/**
	 * Get input data from the previous request.
	 *
	 * Since input data is flashed to the session, a session driver must be specified
	 * in order to use this method.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return string
	 */
	public static function old($key = null, $default = null)
	{
		if (Config::get('session.driver') == '')
		{
			throw new \Exception("Sessions must be enabled to retrieve old input data.");
		}

		return (array_key_exists($key, $old = Session::get('laravel_old_input', array()))) ? $old[$key] : $default;
	}

	/**
	 * Get an item from the uploaded file data.
	 *
	 * If a "dot" is present in the key. A specific element will be returned from 
	 * the specified file array.
	 *
	 *     Example: Input::file('picture.size');
	 *
	 * The statement above will return the value of $_FILES['picture']['size'].
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return array
	 */
	public static function file($key = null, $default = null)
	{
		if (strpos($key, '.') !== false)
		{
			list($file, $key) = explode('.', $key);

			return (isset($_FILES[$file][$key])) ? $_FILES[$file][$key] : $default;
		}

		return (array_key_exists($key, $_FILES)) ? $_FILES[$key] : $default;
	}

	/**
	 * Hydrate the input data for the request.
	 *
	 * Typically, browsers do not support PUT and DELETE methods on HTML forms. So, they are simulated
	 * by Laravel using a hidden POST element. If the request method is being "spoofed", the POST
	 * array will be moved into the PUT / DELETE array. True "PUT" or "DELETE" rqeuests will be read
	 * from the php://input file.
	 *
	 * @return void
	 */
	public static function hydrate()
	{
		switch (Request::method())
		{
			case 'GET':
				static::$input =& $_GET;
				break;

			case 'POST':
				static::$input =& $_POST;
				break;

			case 'PUT':
			case 'DELETE':
				if (isset($_POST['REQUEST_METHOD']) and in_array($_POST['REQUEST_METHOD'], array('PUT', 'DELETE')))
				{
					static::$input =& $_POST;
				}
				else
				{
					parse_str(file_get_contents('php://input'), static::$input);
				}
		}
	}

}