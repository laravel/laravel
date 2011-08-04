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
	 * The module that contains the view.
	 *
	 * @var string
	 */
	public $module;

	/**
	 * The path to the view.
	 *
	 * @var string
	 */
	public $path;

	/**
	 * The defined view composers.
	 *
	 * @var array
	 */
	private static $composers;

	/**
	 * The defined view names.
	 *
	 * @var array
	 */
	private static $names;

	/**
	 * Create a new view instance.
	 *
	 * @param  string  $view
	 * @param  array   $data
	 * @return void
	 */
	public function __construct($view, $data = array())
	{
		$this->data = $data;

		list($this->module, $this->path, $this->view) = static::parse($view);

		if ( ! file_exists($this->path.$this->view.EXT))
		{
			throw new \Exception("View [$view] does not exist.");
		}

		$this->compose();
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
	 * Parse a view identifier and get the module, path, and view name.
	 *
	 * @param  string  $view
	 * @return array
	 */
	private static function parse($view)
	{
		// Check for a module qualifier. If a module name is present, we need to extract it from
		// the view name, otherwise, we will use "application" as the module.
		$module = (strpos($view, '::') !== false) ? substr($view, 0, strpos($view, ':')) : 'application';

		$path = ($module == 'application') ? VIEW_PATH : MODULE_PATH.$module.'/views/';

		// If the view is stored in a module, we need to strip the module qualifier off
		// of the view name before continuing.
		if ($module != 'application')
		{
			$view = substr($view, strpos($view, ':') + 2);
		}

		return array($module, $path, str_replace('.', '/', $view));
	}

	/**
	 * Call the composer for the view instance.
	 *
	 * @return void
	 */
	private function compose()
	{
		if (is_null(static::$composers[$this->module]))
		{
			static::$composers[$this->module] = (file_exists($path = $this->path.'composers'.EXT)) ? require $path : array();
		}

		if (isset(static::$composers[$this->module][$this->view]))
		{
			call_user_func(static::$composers[$this->module][$this->view], $this);
		}
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

		try { include $this->path.$this->view.EXT; } catch (\Exception $e) { Error::handle($e); }

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