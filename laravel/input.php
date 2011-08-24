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
	 * The $_COOKIE array for the request.
	 *
	 * @var array
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
	 * @param  Request  $request
	 * @param  array    $get
	 * @param  array    $post
	 * @param  array    $cookies
	 * @param  array    $files
	 */
	public function __construct(Request $request, $get, $post, $cookies, $files)
	{
		$this->get = $get;
		$this->post = $post;
		$this->files = $files;
		$this->cookies = $cookies;

		$this->hydrate($request->method(), $request->is_spoofed());
	}

	/**
	 * Hydrate the input for a given request.
	 *
	 * @param  string  $method
	 * @param  bool    $spoofed
	 * @return void
	 */
	private function hydrate($method, $spoofed)
	{
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
	 * @param  string          $key
	 * @param  mixed           $default
	 * @param  Session\Driver  $driver
	 * @return string
	 */
	public function old($key = null, $default = null, Session\Driver $driver = null)
	{
		if (is_null($driver)) $driver = Session::driver();

		return Arr::get($driver->get('laravel_old_input', array()), $key, $default);
	}

	/**
	 * Get an item from the uploaded file data.
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