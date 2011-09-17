<?php namespace Laravel;

class Input {

	/**
	 * The file manager instance.
	 *
	 * @var File
	 */
	protected $file;

	/**
	 * The applicable input for the request.
	 *
	 * @var array
	 */
	protected $input;

	/**
	 * The $_FILES array for the request.
	 *
	 * @var array
	 */
	protected $files;

	/**
	 * The cookie engine instance.
	 *
	 * @var Cookie
	 */
	public $cookies;

	/**
	 * Create a new Input manager instance.
	 *
	 * @param  File    $file
	 * @param  Cookie  $cookies
	 * @param  array   $input
	 * @param  array   $files
	 * @return void
	 */
	public function __construct(File $file, Cookie $cookies, $input, $files)
	{
		$this->file = $file;
		$this->input = $input;
		$this->files = $files;
		$this->cookies = $cookies;
	}

	/**
	 * Get all of the input data for the request.
	 *
	 * This method returns a merged array containing $input->get() and $input->files().
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
	public function old($key = null, $default = null)
	{
		if (IoC::container()->resolve('laravel.config')->get('session.driver') == '')
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
	 *		// Move the "picture" file to a permament location on disk
	 *		Input::upload('picture', PUBLIC_PATH.'img/picture.jpg');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  string  $path
	 * @return bool
	 */
	public function upload($key, $path)
	{
		return array_key_exists($key, $this->files) ? $this->file->upload($key, $path, $this->files) : false;
	}

	/**
	 * Magic Method for retrieving items from the request input.
	 *
	 * This method is particularly helpful in controllers where access to the IoC container
	 * is provided through the controller's magic __get method.
	 *
	 * <code>
	 *		// Retrieve the "name" input item from a controller method
	 *		$name = $this->input->name;
	 * </code>
	 */
	public function __get($key)
	{
		return $this->get($key);
	}

}