<?php namespace Laravel\Routing;

use Laravel\IoC;
use Laravel\View;
use Laravel\Request;
use Laravel\Redirect;
use Laravel\Response;

abstract class Controller {

	/**
	 * The layout being used by the controller.
	 *
	 * @var string
	 */
	public $layout;

	/**
	 * The filters assigned to the controller.
	 *
	 * @var array
	 */
	protected $filters = array();

	/**
	 * Handle the delegation of a route to a controller method.
	 *
	 * The controller destination should follow a {controller}@{method} convention.
	 * Nested controllers may be delegated to using dot syntax.
	 *
	 * For example, a destination of "user.profile@show" would call the User_Profile
	 * controller's show method with the given parameters.
	 *
	 * @param  string    $destination
	 * @param  array     $parameters
	 * @return Response
	 */
	public static function call($destination, $parameters = array())
	{
		if (strpos($destination, '@') === false)
		{
			throw new \InvalidArgumentException("Route delegate [{$destination}] has an invalid format.");
		}

		list($controller, $method) = explode('@', $destination);

		$controller = static::resolve($controller);

		if (is_null($controller))
		{
			return Response::error('404');
		}

		return $controller->execute($method, $parameters);
	}

	/**
	 * Resolve a controller name to a controller instance.
	 *
	 * @param  Container   $container
	 * @param  string      $controller
	 * @return Controller
	 */
	public static function resolve($controller)
	{
		if ( ! static::load($controller)) return;

		// If the controller is registered in the IoC container, we will resolve
		// it out of the container. Using constructor injection on controllers
		// via the container allows more flexible and testable applications.
		if (IoC::registered('controllers.'.$controller))
		{
			return IoC::resolve('controllers.'.$controller);
		}

		$controller = str_replace(' ', '_', ucwords(str_replace('.', ' ', $controller))).'_Controller';

		$controller = new $controller;

		// If the controller has specified a layout to be used when rendering
		// views, we will instantiate the layout instance and set it to the
		// layout property, replacing the string layout name.
		if ( ! is_null($controller->layout))
		{
			$controller->layout = View::make($controller->layout);
		}

		return $controller;
	}

	/**
	 * Load the file for a given controller.
	 *
	 * @param  string  $controller
	 * @return bool
	 */
	protected static function load($controller)
	{
		$controller = strtolower(str_replace('.', '/', $controller));

		if (file_exists($path = CONTROLLER_PATH.$controller.EXT))
		{
			require_once $path;

			return true;
		}

		return false;
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
		// Again, as was the case with route closures, if the controller
		// "before" filters return a response, it will be considered the
		// response to the request and the controller method will not be
		// used to handle the request to the application.
		$response = Filter::run($this->filters('before', $method), array(), true);

		if (is_null($response))
		{
			$response = call_user_func_array(array($this, "action_{$method}"), $parameters);

			// If the controller has specified a layout view. The response
			// returned by the controller method will be bound to that view
			// and the layout will be considered the response.
			if (is_null($response) and ! is_null($this->layout))
			{
				$response = $this->layout;
			}
		}

		// The after filter and the framework expects all responses to
		// be instances of the Response class. If the method did not
		// return an instsance of Response, we will make on now.
		if ( ! $response instanceof Response)
		{
			$response = new Response($response);
		}

		Filter::run($this->filters('after', $method), array($response));

		return $response;
	}

	/**
	 * Register filters on the controller's methods.
	 *
	 * Generally, this method will be used in the controller's constructor.
	 *
	 * <code>
	 *		// Set a "foo" after filter on the controller
	 *		$this->filter('before', 'foo');
	 *
	 *		// Set several filters on an explicit group of methods
	 *		$this->filter('after', 'foo|bar')->only(array('user', 'profile'));
	 * </code>
	 *
	 * @param  string|array       $filters
	 * @return Filter_Collection
	 */
	protected function filter($name, $filters)
	{
		$this->filters[$name][] = new Filter_Collection($name, $filters);

		return $this->filters[$name][count($this->filters[$name]) - 1];
	}

	/**
	 * Get an array of filter names defined for the destination.
	 *
	 * @param  string  $name
	 * @param  string  $method
	 * @return array
	 */
	protected function filters($name, $method)
	{
		if ( ! isset($this->filters[$name])) return array();

		$filters = array();

		foreach ($this->filters[$name] as $filter)
		{
			if ($filter->applies($method))
			{
				$filters = array_merge($filters, $filter->filters);
			}
		}

		return array_unique($filters);
	}

	/**
	 * Magic Method to handle calls to undefined functions on the controller.
	 *
	 * By default, the 404 response will be returned for an calls to undefined
	 * methods on the controller. However, this method may also be overridden
	 * and used as a pseudo-router by the controller.
	 */
	public function __call($method, $parameters)
	{
		return Response::error('404');
	}

	/**
	 * Dynamically resolve items from the application IoC container.
	 *
	 * <code>
	 *		// Retrieve an object registered in the container as "mailer"
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

		throw new \OutOfBoundsException("Attempting to access undefined property [$key] on controller.");
	}

}
