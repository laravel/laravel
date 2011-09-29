<?php namespace Laravel;

class Input {

	/**
	 * The applicable input for the request.
	 *
	 * @var array
	 */
	protected $input;

	/**
	 * The $_FILES array for the current request.
	 *
	 * @var array
	 */
	protected $files;

	/**
	 * The key used to store old input in the session.
	 *
	 * @var string
	 */
	const old_input = 'laravel_old_input';

	/**
	 * Create a new input manager instance.
	 *
	 * @param  array  $input
	 * @param  array  $files
	 * @return void
	 */
	public function __construct($input, $files)
	{
		$this->input = $input;
		$this->files = $files;
	}

	/**
	 * Get all of the input data for the request.
	 *
	 * This method returns a merged array containing Input::get() and Input::files().
	 *
	 * @return array
	 */
	public function all()
	{
		return array_merge($this->get(), $this->file());
	}

	/**
	 * Determine if the input data contains an item.
	 *
	 * If the item is in the input array, but is an empty string, false will be returned.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function has($key)
	{
		return ( ! is_null($this->get($key)) and trim((string) $this->get($key)) !== '');
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
	public function get($key = null, $default = null)
	{
		return Arr::get($this->input, $key, $default);
	}

	/**
	 * Determine if the old input data contains an item.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function had($key)
	{
		return ( ! is_null($this->old($key)) and trim((string) $this->old($key)) !== '');
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
	public function old($key = null, $default = null)
	{
		if (Config::get('session.driver') == '')
		{
			throw new \Exception('A session driver must be specified in order to access old input.');
		}

		$driver = IoC::container()->core('session');

		return Arr::get($driver->get(Input::old_input, array()), $key, $default);
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
	public function file($key = null, $default = null)
	{
		return Arr::get($this->files, $key, $default);
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
	public function upload($key, $path)
	{
		return array_key_exists($key, $this->files) ? File::upload($key, $path, $this->files) : false;
	}

}