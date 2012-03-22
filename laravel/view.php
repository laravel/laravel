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
	 * The extensions a view file can have.
	 *
	 * @var array
	 */
	public static $extensions = array(EXT);

	/**
	 * The path in which a view can live.
	 *
	 * @var array
	 */
	public static $paths = array(DEFAULT_BUNDLE => array(''));

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
		$this->path = $this->path($view);

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
	 * @return string
	 */
	public static function file($bundle, $view)
	{
		$root = Bundle::path($bundle).'views/';

		// Views may have either the default PHP fiel extension of the "Blade"
		// extension, so we will need to check for both in the view path
		// and return the first one we find for the given view.
		if (file_exists($path = $root.$view.EXT))
		{
			return $path;
		}
		elseif (file_exists($path = $root.$view.BLADE_EXT))
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
	 * Register a new root path for a bundle.
	 *
	 * @param  string  $bundle
	 * @param  string  $path
	 * @return void
	 */
	public static function search($bundle, $path)
	{
		static::$paths[$bundle][] = $path;
	}

	/**
	 * Register a new valid view extension.
	 *
	 * @param  string  $extension
	 * @return void
	 */
	public static function extension($extension)
	{
		static::$extensions[] = $extension;

		static::$extensions = array_unique(static::$extensions);
	}

	/**
	 * Get the evaluated string content of the view.
	 *
	 * @return string
	 */
	public function render()
	{
		// To allow bundles or other pieces of the application to modify the
		// view before it is rendered, we'll fire an event, passing in the
		// view instance so it can modified.
		$composer = "laravel.composing: {$this->view}";

		Event::fire($composer, array($this));

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

		ob_start() and extract($__data, EXTR_SKIP);

		// We'll include the view contents for parsing within a catcher
		// so we can avoid any WSOD errors. If an exception occurs we
		// will throw it out to the exception handler.
		try
		{
			include $this->path;
		}

		// If we caught an exception, we'll silently flush the output
		// buffer so that no partially rendered views get thrown out
		// to the client and confuse the user.
		catch (\Exception $e)
		{
			ob_get_clean(); throw $e;
		}

		return ob_get_clean();
	}

	/**
	 * Get the array of view data for the view instance.
	 *
	 * The shared view data will be combined with the view data for the instance.
	 *
	 * @return array
	 */
	public function data()
	{
		$data = array_merge($this->data, static::$shared);

		// All nested views and responses are evaluated before the main view.
		// This allows the assets used by nested views to be added to the
		// asset container before the main view is evaluated.
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