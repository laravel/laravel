<?php namespace Laravel;

class Input {

	/**
	 * The applicable input for the request.
	 *
	 * @var array
	 */
	public static $input;

	/**
	 * Get all of the input data for the request.
	 *
	 * This method returns a merged array containing $input->get() and $input->files().
	 *
	 * @return array
	 */
	public static function all()
	{
		return array_merge(static::get(), static::file());
	}

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
	 * This method should be used for all request methods (GET, POST, PUT, and DELETE).
	 *
	 * <code>
	 *		// Get an item from the input to the application
	 *		$value = Input::get('name');
	 *
	 * 		// Get an item from the input and return "Fred" if the item doesn't exist
	 *		$value = Input::get('name', 'Fred');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public static function get($key = null, $default = null)
	{
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
	 * <code>
	 *		// Get an item from the previous request's input
	 *		$value = Input::old('name');
	 *
	 * 		// Get an item from the previous request's input and return "Fred" if it doesn't exist.
	 *		$value = Input::old('name', 'Fred');
	 * </code>
	 *
	 * @param  string          $key
	 * @param  mixed           $default
	 * @return string
	 */
	public static function old($key = null, $default = null)
	{
		if (Config::get('session.driver') == '')
		{
			throw new \Exception('A session driver must be specified in order to access old input.');
		}

		$driver = IoC::container()->resolve('laravel.session');

		return Arr::get($driver->get('laravel_old_input', array()), $key, $default);
	}

	/**
	 * Get an item from the uploaded file data.
	 *
	 * "Dot" syntax may be used to get a specific item from the file array.
	 *
	 * <code>
	 *		// Get the array of information regarding an uploaded file
	 *		$file = Input::file('picture');
	 *
	 *		// Get an element from the array of information regarding an uploaded file
	 *		$size = Input::file('picture.size');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return array
	 */
	public static function file($key = null, $default = null)
	{
		return Arr::get($_FILES, $key, $default);
	}

	/**
	 * Move an uploaded file to permanent storage.
	 *
	 * This method is simply a convenient wrapper around move_uploaded_file.
	 *
	 * <code>
	 *		// Move the "picture" file to a permament location on disk
	 *		Input::upload('picture', PUBLIC_PATH.'img/picture.jpg');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  string  $path
	 * @return bool
	 */
	public static function upload($key, $path)
	{
		return array_key_exists($key, $_FILES) ? File::upload($key, $path, $_FILES) : false;
	}

}

/**
 * Set the input values for the current request.
 */
$input = array();

switch (Request::method())
{
	case 'GET':
		$input = $_GET;
		break;

	case 'POST':
		$input = $_POST;
		break;

	case 'PUT':
	case 'DELETE':
		if (Request::spoofed())
		{
			$input = $_POST;
		}
		else
		{
			parse_str(file_get_contents('php://input'), $input);
		}
}

unset($input[Request::spoofer]);

Input::$input = $input;