<?php namespace Laravel;

class Input {

	/**
	 * The applicable input for the request.
	 *
	 * @var array
	 */
	public $input;

	/**
	 * The $_GET array for the request.
	 *
	 * @var array
	 */
	public $get;

	/**
	 * The $_POST array for the request.
	 *
	 * @var array
	 */
	public $post;

	/**
	 * The cookie manager instance.
	 *
	 * @var Cookie
	 */
	public $cookies;

	/**
	 * The $_FILES array for the request.
	 *
	 * @var array
	 */
	public $files;

	/**
	 * Create a new Input instance.
	 *
	 * @param  string  $method
	 * @param  bool    $spoofed
	 * @param  array   $get
	 * @param  array   $post
	 * @param  array   $files
	 * @param  Cookie  $cookies
	 * @return void
	 */
	public function __construct($method, $spoofed, $get, $post, $files, Cookie $cookies)
	{
		$this->get = $get;
		$this->post = $post;
		$this->files = $files;
		$this->cookies = $cookies;

		if ($method == 'GET')
		{
			$this->input = $this->get;
		}
		elseif ($method == 'POST')
		{
			$this->input = $this->post;
		}
		elseif ($method == 'PUT' or $method == 'DELETE')
		{
			($spoofed) ? $this->input = $this->post : parse_str(file_get_contents('php://input'), $this->input);
		}
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
	 *		// Get the "name" item from the input data
	 *		$name = Request::active()->input->get('name');
	 *
	 *		// Get the "name" item and return "Fred" if it doesn't exist.
	 *		$name = Request::active()->input->get('name', 'Fred');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return string
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
	 *		// Get the "name" item from the old input data
	 *		$name = Request::active()->input->old('name');
	 * </code>
	 *
	 * @param  string          $key
	 * @param  mixed           $default
	 * @return string
	 */
	public function old($key = null, $default = null)
	{
		$driver = IoC::container()->resolve('laravel.session.driver');

		return Arr::get($driver->get('laravel_old_input', array()), $key, $default);
	}

	/**
	 * Get an item from the uploaded file data.
	 *
	 * "Dot" syntax may be used to get a specific item from the file array.
	 *
	 * <code>
	 *		// Get the array of information regarding a given file
	 *		$file = Request::active()->input->file('picture');
	 *
	 *		// Get the size of a given file
	 *		$file = Request::active()->input->file('picture.size');
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
	 * @param  string  $key
	 * @param  string  $path
	 * @return bool
	 */
	public function upload($key, $path)
	{
		return array_key_exists($key, $this->files) ? move_uploaded_file($this->files[$key]['tmp_name'], $path) : false;
	}

	/**
	 * Magic Method for retrieving items from the request input.
	 *
	 * <code>
	 *		// Retrieve the "name" item from the input data
	 *		$name = Request::active()->input->name;
	 * </code>
	 */
	public function __get($key)
	{
		return $this->get($key);
	}

}