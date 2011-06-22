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

		// -----------------------------------------------------
		// Every view has an error collector. This makes it
		// convenient to check for any validation errors without
		// worrying if the error collector is instantiated.
		//
		// If an error collector is in the session, it will
		// be used as the error collector for the view.
		// -----------------------------------------------------
		$this->data['errors'] = (Config::get('session.driver') != '' and Session::has('errors')) ? Session::get('errors') : new Validation\Error_Collector;
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
	 * Get the parsed content of the view.
	 *
	 * @return string
	 */
	public function get()
	{
		static::$last = $this->view;

		// -----------------------------------------------------
		// Get the evaluated content of all of the sub-views.
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
		// Start the output buffer so nothing escapes to the
		// browser. The response will be sent later.
		// -----------------------------------------------------
		ob_start();

		$path = $this->find();

		// -----------------------------------------------------
		// We include the view into the local scope within a
		// try / catch block to catch any exceptions that may
		// occur while the view is rendering.
		//
		// Otherwise, a white screen of death will be shown
		// if an exception occurs while rendering the view.
		// -----------------------------------------------------
		try
		{
			include $path;
		}
		catch (\Exception $e)
		{
			Error::handle($e);
		}

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
	private function find()
	{
		if (file_exists($path = APP_PATH.'views/'.$this->view.EXT))
		{
			return $path;
		}
		elseif (file_exists($path = SYS_PATH.'views/'.$this->view.EXT))
		{
			return $path;
		}
		else
		{
			throw new \Exception("View [".$this->view."] doesn't exist.");
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