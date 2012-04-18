<?php namespace Laravel\Routing;

use Laravel\IoC;
use Laravel\Str;
use Laravel\View;
use Laravel\Event;
use Laravel\Bundle;
use Laravel\Request;
use Laravel\Redirect;
use Laravel\Response;
use FilesystemIterator as fIterator;

abstract class Controller {

	/**
	 * The layout being used by the controller.
	 *
	 * @var string
	 */
	public $layout;

	/**
	 * The bundle the controller belongs to.
	 *
	 * @var string
	 */
	public $bundle;

	/**
	 * Indicates if the controller uses RESTful routing.
	 *
	 * @var bool
	 */
	public $restful = false;

	/**
	 * The filters assigned to the controller.
	 *
	 * @var array
	 */
	protected $filters = array();

	/**
	 * The event name for the Laravel controller factory.
	 *
	 * @var string
	 */
	const factory = 'laravel.controller.factory';

	/**
	 * Create a new Controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		// If the controller has specified a layout to be used when rendering
		// views, we will instantiate the layout instance and set it to the
		// layout property, replacing the string layout name.
		if ( ! is_null($this->layout))
		{
			$this->layout = $this->layout();
		}
	}

	/**
	 * Detect all of the controllers for a given bundle.
	 *
	 * @param  string  $bundle
	 * @param  string  $directory
	 * @return array
	 */
	public static function detect($bundle = DEFAULT_BUNDLE, $directory = null)
	{
		if (is_null($directory))
		{
			$directory = Bundle::path($bundle).'controllers';
		}

		// First we'll get the root path to the directory housing all of
		// the bundle's controllers. This will be used later to figure
		// out the identifiers needed for the found controllers.
		$root = Bundle::path($bundle).'controllers'.DS;

		$controllers = array();

		$items = new fIterator($directory, fIterator::SKIP_DOTS);

		foreach ($items as $item)
		{
			// If the item is a directory, we will recurse back into the function
			// to detect all of the nested controllers and we will keep adding
			// them into the array of controllers for the bundle.
			if ($item->isDir())
			{
				$nested = static::detect($bundle, $item->getRealPath());

				$controllers = array_merge($controllers, $nested);
			}

			// If the item is a file, we'll assume it is a controller and we
			// will build the identifier string for the controller that we
			// can pass into the route's controller method.
			else
			{
				$controller = str_replace(array($root, EXT), '', $item->getRealPath());

				$controller = str_replace(DS, '.', $controller);

				$controllers[] = Bundle::identifier($bundle, $controller);
			}
		}

		return $controllers;
	}

	/**
	 * Call an action method on a controller.
	 *
	 * <code>
	 *		// Call the "show" method on the "user" controller
	 *		$response = Controller::call('user@show');
	 *
	 *		// Call the "user/admin" controller and pass parameters
	 *		$response = Controller::call('user.admin@profile', array($username));
	 * </code>
	 *
	 * @param  string    $destination
	 * @param  array     $parameters
	 * @return Response
	 */
	public static function call($destination, $parameters = array())
	{
		static::references($destination, $parameters);

		list($bundle, $destination) = Bundle::parse($destination);

		// We will always start the bundle, just in case the developer is pointing
		// a route to another bundle. This allows us to lazy load the bundle and
		// improve speed since the bundle is not loaded on every request.
		Bundle::start($bundle);

		list($controller, $method) = explode('@', $destination);

		$controller = static::resolve($bundle, $controller);

		// If the controller could not be resolved, we're out of options and
		// will return the 404 error response. If we found the controller,
		// we can execute the requested method on the instance.
		if (is_null($controller))
		{
			return Event::first('404');
		}

		return $controller->execute($method, $parameters);
	}

	/**
	 * Replace all back-references on the given destination.
	 *
	 * @param  string  $destination
	 * @param  array   $parameters
	 * @return array
	 */
	protected static function references(&$destination, &$parameters)
	{
		// Controller delegates may use back-references to the action parameters,
		// which allows the developer to setup more flexible routes to various
		// controllers with much less code than would be usual.
		foreach ($parameters as $key => $value)
		{
			$search = '(:'.($key + 1).')';

			$destination = str_replace($search, $value, $destination, $count);

			if ($count > 0) unset($parameters[$key]);
		}

		return array($destination, $parameters);
	}

	/**
	 * Resolve a bundle and controller name to a controller instance.
	 *
	 * @param  string      $bundle
	 * @param  string      $controller
	 * @return Controller
	 */
	public static function resolve($bundle, $controller)
	{
		if ( ! static::load($bundle, $controller)) return;

		$identifier = Bundle::identifier($bundle, $controller);

		// If the controller is registered in the IoC container, we will resolve
		// it out of the container. Using constructor injection on controllers
		// via the container allows more flexible applications.
		$resolver = 'controller: '.$identifier;

		if (IoC::registered($resolver))
		{
			return IoC::resolve($resolver);
		}

		$controller = static::format($bundle, $controller);

		// If we couldn't resolve the controller out of the IoC container we'll
		// format the controller name into its proper class name and load it
		// by convention out of the bundle's controller directory.
		if (Event::listeners(static::factory))
		{
			return Event::first(static::factory, $controller);
		}
		else
		{
			return new $controller;
		}
	}

	/**
	 * Load the file for a given controller.
	 *
	 * @param  string  $bundle
	 * @param  string  $controller
	 * @return bool
	 */
	protected static function load($bundle, $controller)
	{
		$controller = strtolower(str_replace('.', '/', $controller));

		if (file_exists($path = Bundle::path($bundle).'controllers/'.$controller.EXT))
		{
			require_once $path;

			return true;
		}

		return false;
	}

	/**
	 * Format a bundle and controller identifier into the controller's class name.
	 *
	 * @param  string  $bundle
	 * @param  string  $controller
	 * @return string
	 */
	protected static function format($bundle, $controller)
	{
		return Bundle::class_prefix($bundle).Str::classify($controller).'_Controller';
	}

	/**
	 * Execute a controller method with the given parameters.
	 *
	 * @param  string    $method
	 * @param  array     $parameters
	 * @return Response
	 */
	public function execute($method, $parameters = array())
	{
		$filters = $this->filters('before', $method);

		// Again, as was the case with route closures, if the controller "before"
		// filters return a response, it will be considered the response to the
		// request and the controller method will not be used.
		$response = Filter::run($filters, array(), true);

		if (is_null($response))
		{
			$this->before();

			$response = $this->response($method, $parameters);
		}

		$response = Response::prepare($response);

		// The "after" function on the controller is simply a convenient hook
		// so the developer can work on the response before it's returned to
		// the browser. This is useful for templating, etc.
		$this->after($response);

		Filter::run($this->filters('after', $method), array($response));

		return $response;
	}

	/**
	 * Execute a controller action and return the response.
	 *
	 * Unlike the "execute" method, no filters will be run and the response
	 * from the controller action will not be changed in any way before it
	 * is returned to the consumer.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function response($method, $parameters = array())
	{
		// The developer may mark the controller as being "RESTful" which
		// indicates that the controller actions are prefixed with the
		// HTTP verb they respond to rather than the word "action".
		if ($this->restful)
		{
			$action = strtolower(Request::method()).'_'.$method;
		}
		else
		{
			$action = "action_{$method}";
		}

		$response = call_user_func_array(array($this, $action), $parameters);

		// If the controller has specified a layout view the response
		// returned by the controller method will be bound to that
		// view and the layout will be considered the response.
		if (is_null($response) and ! is_null($this->layout))
		{
			$response = $this->layout;
		}

		return $response;
	}

	/**
	 * Register filters on the controller's methods.
	 *
	 * <code>
	 *		// Set a "foo" after filter on the controller
	 *		$this->filter('before', 'foo');
	 *
	 *		// Set several filters on an explicit group of methods
	 *		$this->filter('after', 'foo|bar')->only(array('user', 'profile'));
	 * </code>
	 *
	 * @param  string             $event
	 * @param  string|array       $filters
	 * @param  mixed              $parameters
	 * @return Filter_Collection
	 */
	protected function filter($event, $filters, $parameters = null)
	{
		$this->filters[$event][] = new Filter_Collection($filters, $parameters);

		return $this->filters[$event][count($this->filters[$event]) - 1];
	}

	/**
	 * Get an array of filter names defined for the destination.
	 *
	 * @param  string  $event
	 * @param  string  $method
	 * @return array
	 */
	protected function filters($event, $method)
	{
		if ( ! isset($this->filters[$event])) return array();

		$filters = array();

		foreach ($this->filters[$event] as $collection)
		{
			if ($collection->applies($method))
			{
				$filters[] = $collection;
			}
		}

		return $filters;
	}

	/**
	 * Create the layout that is assigned to the controller.
	 *
	 * @return View
	 */
	public function layout()
	{
		if (starts_with($this->layout, 'name: '))
		{
			return View::of(substr($this->layout, 6));
		}

		return View::make($this->layout);
	}

	/**
	 * This function is called before the action is executed.
	 *
	 * @return void
	 */
	public function before() {}

	/**
	 * This function is called after the action is executed.
	 *
	 * @param  Response  $response
	 * @return void
	 */
	public function after($response) {}

	/**
	 * Magic Method to handle calls to undefined controller functions.
	 */
	public function __call($method, $parameters)
	{
		return Response::error('404');
	}

	/**
	 * Dynamically resolve items from the application IoC container.
	 *
	 * <code>
	 *		// Retrieve an object registered in the container
	 *		$mailer = $this->mailer;
	 *
	 *		// Equivalent call using the IoC container instance
	 *		$mailer = IoC::resolve('mailer');
	 * </code>
	 */
	public function __get($key)
	{
		if (IoC::registered($key))
		{
			return IoC::resolve($key);
		}
	}

}