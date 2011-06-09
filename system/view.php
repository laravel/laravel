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
	 * The content of the view.
	 *
	 * @var string
	 */
	public $content = '';

	/**
	 * The name of last rendered view.
	 *
	 * @var string
	 */
	public static $last;

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

		// -----------------------------------------------------
		// Get the contents of the view from the file system.
		// -----------------------------------------------------
		$this->content = $this->load($view);
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
		return new self($view, $data);
	}

	/**
	 * Load the content of a view.
	 *
	 * @param  string  $view
	 * @return string
	 */
	private function load($view)
	{
		// -----------------------------------------------------
		// Is the view in the application directory?
		// -----------------------------------------------------
		if (file_exists($path = APP_PATH.'views/'.$view.EXT))
		{
			return file_get_contents($path);
		}
		// -----------------------------------------------------
		// Is the view in the system directory?
		// -----------------------------------------------------
		elseif (file_exists($path = SYS_PATH.'views/'.$view.EXT))
		{
			return file_get_contents($path);
		}
		// -----------------------------------------------------
		// Could not locate the view... bail out.
		// -----------------------------------------------------
		else
		{
			throw new \Exception("View [$view] doesn't exist.");
		}
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
	 * Get the parsed content of the view.
	 *
	 * @return string
	 */
	public function get()
	{
		// -----------------------------------------------------
		// Set the name of the last rendered view.
		// -----------------------------------------------------
		static::$last = $this->view;

		// -----------------------------------------------------
		// Get the content of all of the sub-views.
		// -----------------------------------------------------
		foreach ($this->data as &$data)
		{
			if ($data instanceof View or $data instanceof Response)
			{
				$data = (string) $data;
			}
		}

		// -----------------------------------------------------
		// Extract the view data into the local scope.
		// -----------------------------------------------------
		extract($this->data, EXTR_SKIP);

		// -----------------------------------------------------
		// Start an output buffer to catch the content.
		// -----------------------------------------------------
		ob_start();

		// -----------------------------------------------------
		// Echo the content of the view.
		// -----------------------------------------------------
		echo eval('?>'.$this->content);

		// -----------------------------------------------------
		// Get the contents of the output buffer.
		// -----------------------------------------------------
		return ob_get_clean();
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