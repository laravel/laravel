<?php namespace Laravel; use Closure, ArrayAccess;

class View implements ArrayAccess {

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
	public $path;

	/**
	 * All of the shared view data.
	 *
	 * @var array
	 */
	public static $shared = array();

	/**
	 * All of the registered view names.
	 *
	 * @var array
	 */
	public static $names = array();

	/**
	 * Create a new view instance.
	 *
	 * <code>
	 *		// Create a new view instance
	 *		$view = new View('home.index');
	 *
	 *		// Create a new view instance of a bundle's view
	 *		$view = new View('admin::home.index');
	 *
	 *		// Create a new view instance with bound data
	 *		$view = new View('home.index', array('name' => 'Taylor'));
	 * </code>
	 *
	 * @param  string  $view
	 * @param  array   $data
	 * @return void
	 */
	public function __construct($view, $data = array())
	{
		$this->view = $view;
		$this->data = $data;
		$this->path = $this->path($view);

		// If a session driver has been specified, we will bind an instance of the
		// validation error message container to every view. If an errors instance
		// exists in the session, we will use that instance.
		//
		// This makes error display in the view extremely convenient, since the
		// developer can always assume they have a message container instance
		// available to them in the view's variables.
		if ( ! isset($this->data['errors']))
		{
			if (Session::started() and Session::has('errors'))
			{
				$this->data['errors'] = Session::get('errors');
			}
			else
			{
				$this->data['errors'] = new Messages;
			}
		}
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

		$root = Bundle::path(Bundle::name($view)).'views/';

		// Views may have the normal PHP extension or the Blade PHP extension, so
		// we need to check if either of them exist in the base views directory
		// for the bundle. We'll check for the PHP extension first since that
		// is probably the more common of the two.
		foreach (array(EXT, BLADE_EXT) as $extension)
		{
			if (file_exists($path = $root.Bundle::element($view).$extension))
			{
				return $path;
			}
		}

		throw new \Exception("View [$view] does not exist.");
	}

	/**
	 * Create a new view instance.
	 *
	 * <code>
	 *		// Create a new view instance
	 *		$view = View::make('home.index');
	 *
	 *		// Create a new view instance of a bundle's view
	 *		$view = View::make('admin::home.index');
	 *
	 *		// Create a new view instance with bound data
	 *		$view = View::make('home.index', array('name' => 'Taylor'));
	 * </code>
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
	 * Create a new view instance of a named view.
	 *
	 * <code>
	 *		// Create a new named view instance
	 *		$view = View::of('profile');
	 *
	 *		// Create a new named view instance with bound data
	 *		$view = View::of('profile', array('name' => 'Taylor'));
	 * </code>
	 *
	 * @param  string  $name
	 * @param  array   $data
	 * @return View
	 */
	public static function of($name, $data = array())
	{
		return new static(static::$names[$name], $data);
	}

	/**
	 * Assign a name to a view.
	 *
	 * <code>
	 *		// Assign a name to a view
	 *		View::name('partials.profile', 'profile');
	 *
	 *		// Resolve an instance of a named view
	 *		$view = View::of('profile');
	 * </code>
	 *
	 * @param  string  $view
	 * @param  string  $name
	 * @return void
	 */
	public static function name($view, $name)
	{
		static::$names[$name] = $view;
	}

	/**
	 * Register a view composer with the Event class.
	 *
	 * <code>
	 *		// Register a composer for the "home.index" view
	 *		View::composer('home.index', function($view)
	 *		{
	 *			$view['title'] = 'Home';
	 *		});
	 * </code>
	 *
	 * @param  string   $view
	 * @param  Closure  $composer
	 * @return void
	 */
	public static function composer($view, $composer)
	{
		Event::listen("laravel.composing: {$view}", $composer);
	}

	/**
	 * Get the evaluated string content of the view.
	 *
	 * @return string
	 */
	public function render()
	{
		// To allow bundles or other pieces of the application to modify the
		// view before it is rendered, we will fire an event, passing in the
		// view instance so it can modified by any of the listeners.
		Event::fire("laravel.composing: {$this->view}", array($this));

		$data = $this->data();

		ob_start() and extract($data, EXTR_SKIP);

		// If the view is Bladed, we need to check the view for changes and
		// get the path to the compiled view file. Otherwise, we'll just
		// use the regular path to the view.
		//
		// Also, if the Blade view has expired or doesn't exist it will be
		// re-compiled and placed in the view storage directory. The Blade
		// views are re-compiled the original view changes.
		if (strpos($this->path, BLADE_EXT) !== false)
		{
			$this->path = $this->compile();
		}

		try {include $this->path;} catch(\Exception $e) {ob_get_clean(); throw $e;}

		return ob_get_clean();
	}

	/**
	 * Get the array of view data for the view instance.
	 *
	 * The shared view data will be combined with the view data for the instance.
	 *
	 * @return array
	 */
	protected function data()
	{
		$data = array_merge($this->data, static::$shared);

		// All nested views and responses are evaluated before the main view.
		// This allows the assets used by nested views to be added to the
		// asset container before the main view is evaluated and dumps
		// the links to the assets into the HTML.
		foreach ($data as &$value) 
		{
			if ($value instanceof View or $value instanceof Response)
			{
				$value = $value->render();
			}
		}

		return $data;
	}

	/**
	 * Get the path to the compiled version of the Blade view.
	 *
	 * @return string
	 */
	protected function compile()
	{
		// Compiled views are stored in the storage directory using the MD5
		// hash of their path. This allows us to easily store the views in
		// the directory without worrying about re-creating the entire
		// application view directory structure.
		$compiled = path('storage').'views/'.md5($this->path);

		// The view will only be re-compiled if the view has been modified
		// since the last compiled version of the view was created or no
		// compiled view exists. Otherwise, the path will be returned
		// without re-compiling the view.
		if ( ! file_exists($compiled) or (filemtime($this->path) > filemtime($compiled)))
		{
			file_put_contents($compiled, Blade::compile($this->path));
		}

		return $compiled;
	}

	/**
	 * Add a view instance to the view data.
	 *
	 * <code>
	 *		// Add a view instance to a view's data
	 *		$view = View::make('foo')->nest('footer', 'partials.footer');
	 *
	 *		// Equivalent functionality using the "with" method
	 *		$view = View::make('foo')->with('footer', View::make('partials.footer'));
	 * </code>
	 *
	 * @param  string  $key
	 * @param  string  $view
	 * @param  array   $data
	 * @return View
	 */
	public function nest($key, $view, $data = array())
	{
		return $this->with($key, static::make($view, $data));
	}

	/**
	 * Add a key / value pair to the view data.
	 *
	 * Bound data will be available to the view as variables.
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
	 * Add a key / value pair to the shared view data.
	 *
	 * Shared view data is accessible to every view created by the application.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public static function share($key, $value)
	{
		static::$shared[$key] = $value;
	}

	/**
	 * Implementation of the ArrayAccess offsetExists method.
	 */
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->data);
	}

	/**
	 * Implementation of the ArrayAccess offsetGet method.
	 */
	public function offsetGet($offset)
	{
		if (isset($this[$offset])) return $this->data[$offset];
	}

	/**
	 * Implementation of the ArrayAccess offsetSet method.
	 */
	public function offsetSet($offset, $value)
	{
		$this->data[$offset] = $value;
	}

	/**
	 * Implementation of the ArrayAccess offsetUnset method.
	 */
	public function offsetUnset($offset)
	{
		unset($this->data[$offset]);
	}

	/**
	 * Magic Method for handling dynamic data access.
	 */
	public function __get($key)
	{
		return $this->data[$key];
	}

	/**
	 * Magic Method for handling the dynamic setting of data.
	 */
	public function __set($key, $value)
	{
		$this->data[$key] = $value;
	}

	/**
	 * Magic Method for checking dynamically-set data.
	 */
	public function __isset($key)
	{
		return isset($this->data[$key]);
	}

	/**
	 * Get the evaluated string content of the view.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->render();
	}

}