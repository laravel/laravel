<?php namespace Laravel;

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
	public $data;

	/**
	 * The path to the view on disk.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Create a new view instance.
	 *
	 * @param  string  $view
	 * @param  array   $data
	 * @return void
	 */
	protected function __construct($view, $data = array())
	{
		$this->view = $view;
		$this->data = $data;
		$this->path = $this->path($view);
	}

	/**
	 * Create a new view instance.
	 *
	 * @param  string         $view
	 * @param  array          $data
	 * @return View
	 */
	public static function make($view, $data = array())
	{
		return new static($view, $data);
	}

	/**
	 * Create a new view instance from a view name.
	 *
	 * View names are defined in the application composers file.
	 *
	 * <code>
	 *		// Create a new named view instance
	 *		$view = View::of('layout');
	 *
	 *		// Create a new named view instance with bound data
	 *		$view = View::of('layout', array('name' => 'Fred'));
	 * </code>
	 *
	 * @param  string  $name
	 * @param  array   $data
	 * @return View
	 */
	public static function of($name, $data = array())
	{
		if ( ! is_null($view = Composer::name($name)))
		{
			return new static($view, $data);
		}

		throw new \Exception("Named view [$name] is not defined.");
	}

	/**
	 * Get the path to a given view on disk.
	 *
	 * @param  string  $view
	 * @return string
	 */
	protected function path($view)
	{
		$view = str_replace('.', '/', $view);

		if (file_exists($path = VIEW_PATH.$view.'.blade'.EXT))
		{
			return $path;
		}
		elseif (file_exists($path = VIEW_PATH.$view.EXT))
		{
			return $path;
		}

		throw new \Exception('View ['.$view.'] does not exist.');
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
		Composer::compose($this);

		foreach ($this->data as &$data) 
		{
			if ($data instanceof View or $data instanceof Response) $data = $data->render();
		}

		ob_start() and extract($this->data, EXTR_SKIP);

		$content = ($this->bladed()) ? Blade::parse($this->path) : file_get_contents($this->path);

		eval('?>'.$content);

		return ob_get_clean();
	}

	/**
	 * Determine if the view is using the blade view engine.
	 *
	 * @return bool
	 */
	protected function bladed()
	{
		return (strpos($this->path, '.blade'.EXT) !== false);
	}

	/**
	 * Add a view instance to the view data.
	 *
	 * <code>
	 *		// Bind a partial view to the view data
	 *		$view->partial('footer', 'partials/footer');
	 *
	 *		// Bind a partial view to the view data with it's own bound data
	 *		$view->partial('footer', 'partials/footer', array('name' => 'Fred'));
	 * </code>
	 *
	 * @param  string  $key
	 * @param  string  $view
	 * @param  array   $data
	 * @return View
	 */
	public function partial($key, $view, $data = array())
	{
		return $this->with($key, new static($view, $data));
	}

	/**
	 * Add a key / value pair to the view data.
	 *
	 * Bound data will be available to the view as variables.
	 *
	 * <code>
	 *		// Bind a piece of data to a view instance
	 *		$view->with('name', 'Fred');
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

	/**
	 * Magic Method for handling the dynamic creation of named views.
	 *
	 * <code>
	 *		// Create an instance of the "layout" named view
	 *		$view = View::of_layout();
	 *
	 *		// Create an instance of the "layout" named view with bound data
	 *		$view = View::of_layout(array('name' => 'Fred'));
	 * </code>
	 */
	public static function __callStatic($method, $parameters)
	{
		if (strpos($method, 'of_') === 0)
		{
			return static::of(substr($method, 3), Arr::get($parameters, 0, array()));
		}
	}

}

/**
 * The view composer class is responsible for calling the composer on a view and
 * searching through the view composers for a given view name.
 */
class Composer {

	/**
	 * The view composers.
	 *
	 * @var array
	 */
	public static $composers;

	/**
	 * Find the key for a view by name.
	 *
	 * @param  string  $name
	 * @return string
	 */
	public static function name($name)
	{
		foreach (static::$composers as $key => $value)
		{
			if ($name === $value or (isset($value['name']) and $name === $value['name'])) { return $key; }
		}
	}

	/**
	 * Call the composer for the view instance.
	 *
	 * @param  View  $view
	 * @return void
	 */
	public static function compose(View $view)
	{
		if (isset(static::$composers['shared'])) call_user_func(static::$composers['shared'], $view);

		if (isset(static::$composers[$view->view]))
		{
			foreach ((array) static::$composers[$view->view] as $key => $value)
			{
				if ($value instanceof \Closure) return call_user_func($value, $view);
			}
		}
	}

}

/**
 * Load the application's composers into the composers property.
 */
Composer::$composers = require APP_PATH.'composers'.EXT;