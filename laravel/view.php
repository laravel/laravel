<?php namespace Laravel;

class View implements Renderable {

	/**
	 * The name of the view.
	 *
	 * @var string
	 */
	public $view;

	/**
	 * The view name with dots replaced with slashes.
	 *
	 * @var string
	 */
	protected $path;

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
	 * The defined view composers.
	 *
	 * @var array
	 */
	public static $composers;

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
		$this->path = str_replace('.', '/', $view);
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
	protected static function of($name, $data = array())
	{
		if (is_null(static::$composers)) static::$composers = require APP_PATH.'composers'.EXT;

		foreach (static::$composers as $key => $value)
		{
			if ($name === $value or (isset($value['name']) and $name === $value['name']))
			{
				return new static($key, $data);
			}
		}

		throw new \Exception("Named view [$name] is not defined.");
	}

	/**
	 * Call the composer for the view instance.
	 *
	 * @return void
	 */
	protected function compose()
	{
		if (is_null(static::$composers)) static::$composers = require APP_PATH.'composers'.EXT;

		if (isset(static::$composers[$this->view]))
		{
			foreach ((array) static::$composers[$this->view] as $key => $value)
			{
				if (is_callable($value)) return call_user_func($value, $this);
			}
		}
	}

	/**
	 * Get the evaluated string content of the view.
	 *
	 * @return string
	 */
	public function render()
	{
		$this->compose();

		if ( ! file_exists(VIEW_PATH.$this->path.EXT))
		{
			Exception\Handler::make(new Exception('View ['.$this->path.'] does not exist.'))->handle();
		}

		foreach ($this->data as &$data) 
		{
			if ($data instanceof Renderable) $data = $data->render();
		}

		ob_start() and extract($this->data, EXTR_SKIP);

		try { include VIEW_PATH.$this->path.EXT; } catch (\Exception $e) { Exception\Handler::make($e)->handle(); }

		return ob_get_clean();
	}

	/**
	 * Add a view instance to the view data.
	 *
	 * <code>
	 *		// Bind the view "partial/login" to the view
	 *		View::make('home')->partial('login', 'partial/login');
	 *
	 *		// Equivalent binding using the "bind" method
	 *		View::make('home')->bind('login', View::make('partials/login'));
	 * </code>
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
	 * Bound data will be available to the view as variables.
	 *
	 * <code>
	 *		// Bind a "name" value to the view
	 *		View::make('home')->bind('name', 'Fred');
	 * </code>
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
	 *
	 * <code>
	 *		// Create an instance of the "login" named view
	 *		$view = View::of_login();
	 *
	 *		// Create an instance of the "login" named view and bind data to the view
	 *		$view = View::of_login(array('name' => 'Fred'));
	 * </code>
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

}