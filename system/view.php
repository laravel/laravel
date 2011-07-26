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
		$this->path = $this->find();
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
		return new static($view, $data);
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
		// Get the evaluated content of all of the sub-views.
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
	 * Get the full path to the view.
	 *
	 * Views are cascaded, so the application directory views will take
	 * precedence over system directory views of the same name.
	 *
	 * @return string
	 */
	protected function find()
	{
		if (file_exists($path = VIEW_PATH.$this->view.EXT))
		{
			return $path;
		}
		elseif (file_exists($path = SYS_VIEW_PATH.$this->view.EXT))
		{
			return $path;
		}
		else
		{
			throw new \Exception("View [".$this->view."] doesn't exist.");
		}
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