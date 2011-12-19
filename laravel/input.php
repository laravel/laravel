<?php namespace Laravel;

class Input {

	/**
	 * The applicable input for the request.
	 *
	 * @var array
	 */
	public static $input;

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
		return array_merge(static::get(), static::file());
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
		return array_get(static::$input, $key, $default);
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
		return array_diff_key(static::get(), array_flip($keys));
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
	 *
	 *		// Get a specific element from within the file's data array
	 *		$size = Input::file('picture.size');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return array
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
	 *		// Move the "picture" file to a permanent location on disk
	 *		Input::upload('picture', 'path/to/photos/picture.jpg');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  string  $path
	 * @return bool
	 */
	public static function upload($key, $path)
	{
		if (is_null(static::file($key))) return false;

		return move_uploaded_file(static::file("{$key}.tmp_name"), $path);
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

}