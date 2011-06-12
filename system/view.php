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
		if (file_exists($path = APP_PATH.'views/'.$view.EXT))
		{
			return file_get_contents($path);
		}
		elseif (file_exists($path = SYS_PATH.'views/'.$view.EXT))
		{
			return file_get_contents($path);
		}
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

		extract($this->data, EXTR_SKIP);

		ob_start();

		echo eval('?>'.$this->load($this->view));

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