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
	 * The cache content of loaded view files.
	 *
	 * @var array
	 */
	public static $cache = array();

	/**
	 * The Laravel view loader event name.
	 *
	 * @var string
	 */
	const loader = 'laravel.view.loader';

	/**
	 * The Laravel view engine event name.
	 *
	 * @var string
	 */
	const engine = 'laravel.view.engine';

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

		// In order to allow developers to load views outside of the normal loading
		// conventions, we'll allow for a raw path to be given in place of the
		// typical view name, giving total freedom on view loading.
		if (starts_with($view, 'path: '))
		{
			$this->path = substr($view, 6);
		}
		else
		{
			$this->path = $this->path($view);
		}

		// If a session driver has been specified, we will bind an instance of the
		// validation error message container to every view. If an error instance
		// exists in the session, we will use that instance.
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
		list($bundle, $view) = Bundle::parse($view);

		$view = str_replace('.', '/', $view);

		// We delegate the determination of view paths to the view loader event
		// so that the developer is free to override and manage the loading
		// of views in any way they see fit for their application.
		$path = Event::first(static::loader, array($bundle, $view));

		if ( ! is_null($path))
		{
			return $path;
		}

		throw new \Exception("View [$view] doesn't exist.");
	}

	/**
	 * Get the path to a view using the default folder convention.
	 *
	 * @param  string  $bundle
	 * @param  string  $view
	 * @param  string  $directory
	 * @return string
	 */
	public static function file($bundle, $view, $directory)
	{
		$directory = str_finish($directory, DS);

		// Views may have either the default PHP file extension of the "Blade"
		// extension, so we will need to check for both in the view path
		// and return the first one we find for the given view.
		if (file_exists($path = $directory.$view.EXT))
		{
			return $path;
		}
		elseif (file_exists($path = $directory.$view.BLADE_EXT))
		{
			return $path;
		}
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
	 * Get the rendered contents of a partial from a loop.
	 *
	 * @param  string  $view
	 * @param  array   $data
	 * @param  string  $iterator
	 * @param  string  $empty
	 * @return string
	 */
	public static function render_each($view, array $data, $iterator, $empty = 'raw|')
	{
		$result = '';

		// If is actually data in the array, we will loop through the data and
		// append an instance of the partial view to the final result HTML,
		// passing in the iterated value of the data array.
		if (count($data) > 0)
		{
			foreach ($data as $key => $value)
			{
				$with = array('key' => $key, $iterator => $value);

				$result .= render($view, $with);
			}
		}

		// If there is no data in the array, we will render the contents of
		// the "empty" view. Alternative, the "empty view" can be a raw
		// string that is prefixed with "raw|" for convenience.
		else
		{
			if (starts_with($empty, 'raw|'))
			{
				$result = substr($empty, 4);
			}
			else
			{
				$result = render($empty);
			}
		}

		return $result;
	}

	/**
	 * Get the evaluated string content of the view.
	 *
	 * @return string
	 */
	public function render()
	{
		Event::fire("laravel.composing: {$this->view}", array($this));

		// If there are listeners to the view engine event, we'll pass them
		// the view so they can render it according to their needs, which
		// allows easy attachment of other view parsers.
		if (Event::listeners(static::engine))
		{
			$result = Event::first(static::engine, array($this));

			if ($result !== false) return $result;
		}

		return $this->get();
	}

	/**
	 * Get the evaluated contents of the view.
	 *
	 * @return string
	 */
	public function get()
	{
		$__data = $this->data();

		// The contents of each view file is cached in an array for the
		// request since partial views may be rendered inside of for
		// loops which could incur performance penalties.
		$__contents = $this->load();

		ob_start() and extract($__data, EXTR_SKIP);

		// We'll include the view contents for parsing within a catcher
		// so we can avoid any WSOD errors. If an exception occurs we
		// will throw it out to the exception handler.
		try
		{
			eval('?>'.$__contents);
		}

		// If we caught an exception, we'll silently flush the output
		// buffer so that no partially rendered views get thrown out
		// to the client and confuse the user with junk.
		catch (\Exception $e)
		{
			ob_get_clean(); throw $e;
		}

		return ob_get_clean();
	}

	/**
	 * Get the contents of the view file from disk.
	 *
	 * @return string
	 */
	protected function load()
	{
		if (isset(static::$cache[$this->path]))
		{
			return static::$cache[$this->path];
		}
		else
		{
			return static::$cache[$this->path] = include $this->path;
		}
	}

	/**
	 * Get the array of view data for the view instance.
	 *
	 * The shared view data will be combined with the view data.
	 *
	 * @return array
	 */
	public function data()
	{
		$data = array_merge($this->data, static::$shared);

		// All nested views and responses are evaluated before the main view.
		// This allows the assets used by nested views to be added to the
		// asset container before the main view is evaluated.
		foreach ($data as $key => $value) 
		{
			if ($value instanceof View or $value instanceof Response)
			{
				$data[$key] = $value->render();
			}
		}

		return $data;
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
	public function with($key, $value = null)
	{
		if (is_array($key))
		{
			$this->data = array_merge($this->data, $key);
		}
		else
		{
			$this->data[$key] = $value;
		}

		return $this;
	}

	/**
	 * Add a key / value pair to the shared view data.
	 *
	 * Shared view data is accessible to every view created by the application.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return View
	 */
	public function shares($key, $value)
	{
		static::share($key, $value);
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