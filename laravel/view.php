<?php namespace Laravel;

class View_Factory {

	/**
	 * Create a new view factory instance.
	 *
	 * @param  array   $composers
	 * @param  string  $path
	 * @return void
	 */
	public function __construct($composers, $path)
	{
		$this->path = $path;
		$this->composers = $composers;
	}

	/**
	 * Create a new view instance.
	 *
	 * @param  string  $view
	 * @param  array   $data
	 * @return View
	 */
	public function make($view, $data = array())
	{
		return new View($view, $this->path, $data, $this->composers, $this);
	}

	/**
	 * Create a new view instance from a view name.
	 *
	 * @param  string  $name
	 * @param  array   $data
	 * @return View
	 */
	protected function of($name, $data = array())
	{
		foreach ($this->composers as $key => $value)
		{
			if ($name === $value or (isset($value['name']) and $name === $value['name']))
			{
				return new View($key, $this->path, $data, $this->composers, $this);
			}
		}

		throw new \Exception("Named view [$name] is not defined.");
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
	public function __call($method, $parameters)
	{
		if (strpos($method, 'of_') === 0)
		{
			return $this->of(substr($method, 3), Arr::get($parameters, 0, array()));
		}
	}

}

class View {

	/**
	 * The name of the view.
	 *
	 * @var string
	 */
	public $view;

	/**
	 * The view name with dots replaced by slashes.
	 *
	 * @var string
	 */
	public $path;

	/**
	 * The view data.
	 *
	 * @var array
	 */
	public $data;

	/**
	 * The view composers defined for the application.
	 *
	 * @var  array  $composers
	 */
	protected $composers;

	/**
	 * The view factory instance, which is used to create sub-views.
	 *
	 * @var View_Factory
	 */
	protected $factory;

	/**
	 * Create a new view instance.
	 *
	 * @param  string  $view
	 * @param  array   $data
	 * @param  string  $path
	 * @param  array   $composers
	 * @return void
	 */
	public function __construct($view, $path, $data, $composers, $factory)
	{
		$this->view = $view;
		$this->data = $data;
		$this->factory = $factory;
		$this->composers = $composers;
		$this->path = $path.str_replace('.', '/', $view).EXT;

		if ( ! file_exists($this->path))
		{
			throw new \Exception('View ['.$this->path.'] does not exist.');
		}
	}

	/**
	 * Call the composer for the view instance.
	 *
	 * @return void
	 */
	protected function compose()
	{
		if (isset($this->composers[$this->view]))
		{
			foreach ((array) $this->composers[$this->view] as $key => $value)
			{
				if ($value instanceof \Closure) return call_user_func($value, $this);
			}
		}
	}

	/**
	 * Get the evaluated string content of the view.
	 *
	 * If the view has a composer, it will be executed. All sub-views and responses will
	 * also be evaluated and converted to their string values.
	 *
	 * @return string
	 */
	public function render()
	{
		$this->compose();

		foreach ($this->data as &$data) 
		{
			if ($data instanceof View or $data instanceof Response) $data = $data->render();
		}

		ob_start() and extract($this->data, EXTR_SKIP);

		try
		{
			include $this->path;
		}
		catch (\Exception $e)
		{
			Exception\Handler::make(new Exception\Examiner($e))->handle();
		}

		return ob_get_clean();
	}

	/**
	 * Add a view instance to the view data.
	 *
	 * <code>
	 *		// Bind the view "partial/login" to the view
	 *		View::make('home')->partial('login', 'partial/login');
	 *
	 *		// Equivalent binding using the "with" method
	 *		View::make('home')->with('login', View::make('partials/login'));
	 * </code>
	 *
	 * @param  string  $key
	 * @param  string  $view
	 * @param  array   $data
	 * @return View
	 */
	public function partial($key, $view, $data = array())
	{
		return $this->with($key, $this->factory->make($view, $data));
	}

	/**
	 * Add a key / value pair to the view data.
	 *
	 * Bound data will be available to the view as variables.
	 *
	 * <code>
	 *		// Bind a "name" value to the view
	 *		View::make('home')->with('name', 'Fred');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return View
	 */
	public function with($key, $value)
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
		$this->with($key, $value);
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