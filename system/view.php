<?php namespace System;

class View {

	/**
	 * The name of the view.
	 *
	 * @var string
	 */
	public $view;

	/**
	 * The view data.
	 *
	 * @var array
	 */
	public $data = array();

	/**
	 * The path to the view.
	 *
	 * @var string
	 */
	public $path;

	/**
	 * The view composers.
	 *
	 * @var array
	 */
	private static $composers;

	/**
	 * Create a new view instance.
	 *
	 * @param  string  $view
	 * @param  array   $data
	 * @return void
	 */
	public function __construct($view, $data = array())
	{
		$this->view = $view;
		$this->data = $data;

		if ( ! file_exists($path = VIEW_PATH.$view.EXT))
		{
			throw new \Exception("View [$view] does not exist.");
		}

		$this->path = $path;
	}

	/**
	 * Create a new view instance.
	 *
	 * @param  string  $view
	 * @param  array   $data
	 * @return View
	 */
	public static function make($view, $data = array())
	{
		if (is_null(static::$composers))
		{
			static::$composers = require VIEW_PATH.'composers'.EXT;
		}

		$instance = new static($view, $data);

		return (isset(static::$composers[$view])) ? call_user_func(static::$composers[$view], $instance) : $instance;
	}

	/**
	 * Create a new named view instance.
	 *
	 * @param  string  $view
	 * @param  array   $data
	 * @return View
	 */
	public static function of($view, $data = array())
	{
		$views = Config::get('view.names');

		if ( ! array_key_exists($view, $views))
		{
			throw new \Exception("Named view [$view] is not defined.");
		}

		return static::make($views[$view], $data);
	}

	/**
	 * Get the parsed content of the view.
	 *
	 * @return string
	 */
	public function get()
	{
		foreach ($this->data as &$data)
		{
			if ($data instanceof View or $data instanceof Response)
			{
				$data = (string) $data;
			}
		}

		extract($this->data, EXTR_SKIP);

		ob_start();

		try { include $this->path; } catch (\Exception $e) { Error::handle($e); }

		return ob_get_clean();
	}

	/**
	 * Add a view instance to the view data.
	 *
	 * @param  string  $key
	 * @param  string  $view
	 * @param  array   $data
	 * @return View
	 */
	public function partial($key, $view, $data = array())
	{
		return $this->bind($key, static::make($view, $data));
	}

	/**
	 * Add a key / value pair to the view data.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return View
	 */
	public function bind($key, $value)
	{
		$this->data[$key] = $value;
		return $this;
	}

	/**
	 * Magic Method for creating named view instances.
	 */
	public static function __callStatic($method, $parameters)
	{
		if (strpos($method, 'of_') === 0)
		{
			return static::of(substr($method, 3), Arr::get($parameters, 0, array()));
		}
	}

	/**
	 * Magic Method for getting items from the view data.
	 */
	public function __get($key)
	{
		return $this->data[$key];
	}

	/**
	 * Magic Method for setting items in the view data.
	 */
	public function __set($key, $value)
	{
		$this->bind($key, $value);
	}

	/**
	 * Magic Method for determining if an item is in the view data.
	 */
	public function __isset($key)
	{
		return array_key_exists($key, $this->data);
	}

	/**
	 * Magic Method for removing an item from the view data.
	 */
	public function __unset($key)
	{
		unset($this->data[$key]);
	}

	/**
	 * Get the parsed content of the View.
	 */
	public function __toString()
	{
		return $this->get();
	}

}