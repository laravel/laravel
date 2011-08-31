<?php namespace Laravel;

class Input {

	/**
	 * The applicable input for the request.
	 *
	 * @var array
	 */
	private $input;

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
	 * The cookie engine instance.
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
	 * @param  array   $input
	 * @param  array   $files
	 * @param  Cookie  $cookies
	 * @return void
	 */
	public function __construct($input, $files, Cookie $cookies)
	{
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
	 * @param  string          $key
	 * @param  mixed           $default
	 * @return string
	 */
	public function old($key = null, $default = null)
	{
		$driver = IoC::container()->resolve('laravel.session');

		return Arr::get($driver->get('laravel_old_input', array()), $key, $default);
	}

	/**
	 * Get an item from the uploaded file data.
	 *
	 * "Dot" syntax may be used to get a specific item from the file array.
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
	 */
	public function __get($key)
	{
		return $this->get($key);
	}

}