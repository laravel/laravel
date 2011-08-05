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
	 * Create a new view instance from a view name.
	 *
	 * @param  string  $name
	 * @param  array   $data
	 * @return View
	 */
	private static function of($name, $data = array())
	{
		// Search the active module first, then the application module, then all other modules.
		$modules = array_unique(array_merge(array(ACTIVE_MODULE, 'application'), Config::get('application.modules')));

		foreach ($modules as $module)
		{
			static::load_composers($module);

			if ( ! is_null($view = static::find_view_for_name($name, static::$composers[$module])))
			{
				return new static($view, $data);	
			}
		}

		throw new \Exception("Named view [$name] is not defined.");
	}

	/**
	 * Find the view for a given name in an array of composers.
	 *
	 * @param  string  $name
	 * @param  array   $composers
	 * @return string
	 */
	private static function find_view_for_name($name, $composers)
	{
		foreach ($composers as $key => $value)
		{
			if (is_string($value) and $value == $name)
			{
				return $key;
			}
			elseif (is_array($value) and isset($value['name']) and $value['name'] == $name)
			{
				return $key;
			}
		}
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

		return array($module, $path, $view);
	}

	/**
	 * Call the composer for the view instance.
	 *
	 * @return void
	 */
	private function compose()
	{
		static::load_composers($this->module);

		if (isset(static::$composers[$this->module][$this->view]))
		{
			$composer = static::$composers[$this->module][$this->view];

			if ( ! is_null($composer = $this->find_composer_handler($composer)))
			{
				call_user_func($composer, $this);
			}
		}
	}

	/**
	 * Find the composer handler / function in a composer definition.
	 *
	 * If the composer value itself is callable, it will be returned, otherwise the
	 * first callable value in the composer array will be returned.
	 *
	 * @param  mixed    $composer
	 * @return Closure
	 */
	private function find_composer_handler($composer)
	{
		if (is_string($composer)) return;

		if (is_callable($composer)) return $composer;

		foreach ($composer as $value)
		{
			if (is_callable($value)) return $value;
		}
	}	

	/**
	 * Load the view composers for a given module.
	 *
	 * @param  string  $module
	 * @return void
	 */
	private static function load_composers($module)
	{
		if (isset(static::$composers[$module])) return;

		$composers = ($module == 'application') ? APP_PATH.'composers'.EXT : MODULE_PATH.$module.'/composers'.EXT;

		static::$composers[$module] = (file_exists($composers)) ? require $composers : array();
	}

	/**
	 * Get the parsed content of the view.
	 *
	 * @return string
	 */
	public function get()
	{
		$view = str_replace('.', '/', $this->view);

		if ( ! file_exists($this->path.$view.EXT))
		{
			throw new \Exception("View [$view] does not exist.");
		}

		$this->get_sub_views();

		extract($this->data, EXTR_SKIP);

		ob_start();

		try { include $this->path.$view.EXT; } catch (\Exception $e) { Error::handle($e); }

		return ob_get_clean();
	}

	/**
	 * Evaluate all of the view and response instances that are bound to the view.
	 *
	 * @return void
	 */
	private function get_sub_views()
	{
		foreach ($this->data as &$data)
		{
			if ($data instanceof View or $data instanceof Response)
			{
				$data = (string) $data;
			}
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
		return $this->bind($key, new static($view, $data));
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
	 * Magic Method for handling the dynamic creation of named views.
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