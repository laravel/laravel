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

		return Arr::get(static::$input, $key, $default);
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
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return string
	 */
	public static function old($key = null, $default = null)
	{
		// ----------------------------------------------------------
		// Since old input data is flashed to the session, we need
		// to make sure a session driver has been specified.
		// ----------------------------------------------------------
		if (Config::get('session.driver') == '')
		{
			throw new \Exception("Sessions must be enabled to retrieve old input data.");
		}

		return Arr::get(Session::get('laravel_old_input', array()), $key, $default);
	}

	/**
	 * Hydrate the input data for the request.
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
				// ----------------------------------------------------------------------
				// Typically, browsers do not support PUT and DELETE methods on HTML
				// forms. So, we simulate them using a hidden POST variable.
				//
				// If the request method is being "spoofed", we'll move the POST array
				// into the PUT / DELETE array.
				// ----------------------------------------------------------------------
				if (isset($_POST['request_method']) and ($_POST['request_method'] == 'PUT' or $_POST['request_method'] == 'DELETE'))
				{
					static::$input =& $_POST;
				}
				// ----------------------------------------------------------------------
				// If the request is a true PUT request, read the php://input file.
				// ----------------------------------------------------------------------
				else
				{
					parse_str(file_get_contents('php://input'), static::$input);
				}
		}
	}

}