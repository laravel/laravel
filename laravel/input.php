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
	 * Get all of the input data for the request.
	 *
	 * This method returns a merged array containing Input::get() and Input::files().
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
	 * If the item is in the input array, but is an empty string, false will be returned.
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
	 * This method should be used for all request methods (GET, POST, PUT, and DELETE).
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
		return Arr::get(static::$input, $key, $default);
	}

	/**
	 * Flash the input for the current request to the session.
	 *
	 * The input data to be flashed may be controlled by using a filter and an array
	 * of included or excluded input data. This provides a convenient way of keeping
	 * sensitive information like passwords out of the session.
	 *
	 * <code>
	 *		// Flash all of the input data to the session
	 *		Input::flash();
	 *
	 *		// Flash only a few input items to the session
	 *		Input::flash('only', array('name', 'email'));
	 *
	 *		// Flash all but a few input items to the session
	 *		Input::flash('except', array('password'));
	 * </code>
	 *
	 * @return void
	 */
	public static function flash($filter = null, $items = array())
	{
		$flash = static::get();

		// Since the items flashed to the session can be filtered, we will iterate
		// all of the input data and either remove or include the input item based
		// on the specified filter and array of items to be flashed.
		if ($filter == 'only')
		{
			$flash = array_intersect_key($flash, array_flip($items));
		}
		elseif ($filter == 'except')
		{
			$flash = array_diff_key($flash, array_flip($items));
		}

		IoC::core('session')->flash(Input::old_input, $flash);
	}

	/**
	 * Flush the old input from the session.
	 *
	 * @return void
	 */
	public static function flush()
	{
		IoC::core('session')->flash(Input::old_input, array());
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
		$old = IoC::core('session')->get(Input::old_input, array());

		return Arr::get($old, $key, $default);
	}

	/**
	 * Get an item from the uploaded file data.
	 *
	 * <code>
	 *		// Get the array of information for the "picture" upload
	 *		$picture = Input::file('picture');
	 *
	 *		// Get a specific element from the file array
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
	 *		// Move the "picture" item from the $_FILES array to a permanent location
	 *		Input::upload('picture', 'path/to/storage/picture.jpg');
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
