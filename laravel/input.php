<?php namespace Laravel;

class Input {

	/**
	 * The key used to store old input in the session.
	 *
	 * @var string
	 */
	const old_input = 'laravel_old_input';

	/**
	 * Get all of the input data for the request, including files.
	 *
	 * @return array
	 */
	public static function all()
	{
		$input = array_merge_recursive(static::get(), static::query(), static::file());

		unset($input[Request::spoofer]);

		return $input;
	}

	/**
	 * Determine if the input data contains an item.
	 *
	 * If the input item is an empty string, false will be returned.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public static function has($key)
	{
		return trim((string) static::get($key)) !== '';
	}

	/**
	 * Get an item from the input data.
	 *
	 * This method is used for all request verbs (GET, POST, PUT, and DELETE).
	 *
	 * <code>
	 *		// Get the "email" item from the input array
	 *		$email = Input::get('email');
	 *
	 *		// Return a default value if the specified item doesn't exist
	 *		$email = Input::get('name', 'Taylor');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public static function get($key = null, $default = null)
	{
		$input = Request::foundation()->request->all();

		if (is_null($key))
		{
			return array_merge($input, static::query());
		}

		$value = array_get($input, $key);

		if (is_null($value))
		{
			return array_get(static::query(), $key, $default);
		}

		return $value;
	}

	/**
	 * Get an item from the query string.
	 *
	 * <code>
	 *		// Get the "email" item from the query string
	 *		$email = Input::query('email');
	 *
	 *		// Return a default value if the specified item doesn't exist
	 *		$email = Input::query('name', 'Taylor');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public static function query($key = null, $default = null)
	{
		return array_get(Request::foundation()->query->all(), $key, $default);
	}

	/**
	 * Get a subset of the items from the input data.
	 *
	 * <code>
	 *		// Get only the email from the input data
	 *		$value = Input::only('email');
	 *
	 *		// Get only the username and email from the input data
	 *		$input = Input::only(array('username', 'email'));
	 * </code>
	 *
	 * @param  array  $keys
	 * @return array
	 */
	public static function only($keys)
	{
 		return array_intersect_key(static::get(), array_flip((array) $keys));
	}

	/**
	 * Get all of the input data except for a specified array of items.
	 *
	 * <code>
	 *		// Get all of the input data except for username
	 *		$input = Input::except('username');
	 *
	 *		// Get all of the input data except for username and email
	 *		$input = Input::except(array('username', 'email'));
	 * </code>
	 *
	 * @param  array  $keys
	 * @return array
	 */
	public static function except($keys)
	{
		return array_diff_key(static::get(), array_flip((array) $keys));
	}

	/**
	 * Determine if the old input data contains an item.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public static function had($key)
	{
		return trim((string) static::old($key)) !== '';
	}

	/**
	 * Get input data from the previous request.
	 *
	 * <code>
	 *		// Get the "email" item from the old input
	 *		$email = Input::old('email');
	 *
	 *		// Return a default value if the specified item doesn't exist
	 *		$email = Input::old('name', 'Taylor');
	 * </code>
	 *
	 * @param  string          $key
	 * @param  mixed           $default
	 * @return string
	 */
	public static function old($key = null, $default = null)
	{
		return array_get(Session::get(Input::old_input, array()), $key, $default);
	}

	/**
	 * Get an item from the uploaded file data.
	 *
	 * <code>
	 *		// Get the array of information for the "picture" upload
	 *		$picture = Input::file('picture');
	 * </code>
	 *
	 * @param  string        $key
	 * @param  mixed         $default
	 * @return UploadedFile
	 */
	public static function file($key = null, $default = null)
	{
		return array_get($_FILES, $key, $default);
	}

	/**
	 * Move an uploaded file to permanent storage.
	 *
	 * This method is simply a convenient wrapper around move_uploaded_file.
	 *
	 * <code>
	 *		// Move the "picture" file to a new permanent location on disk
	 *		Input::upload('picture', 'path/to/photos', 'picture.jpg');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  string  $directory
	 * @param  string  $name
	 * @return bool
	 */
	public static function upload($key, $directory, $name = null)
	{
		if (is_null(static::file($key))) return false;

		return Request::foundation()->files->get($key)->move($directory, $name);
	}

	/**
	 * Flash the input for the current request to the session.
	 *
	 * <code>
	 *		// Flash all of the input to the session
	 *		Input::flash();
	 *
	 *		// Flash only a few input items to the session
	 *		Input::flash('only', array('name', 'email'));
	 *
	 *		// Flash all but a few input items to the session
	 *		Input::flash('except', array('password', 'social_number'));
	 * </code>
	 *
	 * @param  string  $filter
	 * @param  array   $keys
	 * @return void
	 */
	public static function flash($filter = null, $keys = array())
	{
		$flash = ( ! is_null($filter)) ? static::$filter($keys) : static::get();

		Session::flash(Input::old_input, $flash);
	}

	/**
	 * Flush all of the old input from the session.
	 *
	 * @return void
	 */
	public static function flush()
	{
		Session::flash(Input::old_input, array());
	}

	/**
	 * Merge new input into the current request's input array.
	 *
	 * @param  array  $input
	 * @return void
	 */
	public static function merge(array $input)
	{
		Request::foundation()->request->add($input);
	}

	/**
	 * Replace the input for the current request.
	 *
	 * @param  array  $input
	 * @return void
	 */
	public static function replace(array $input)
	{
		Request::foundation()->request->replace($input);
	}

}