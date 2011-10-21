<?php namespace Laravel\Routing;

use Laravel\IoC;
use Laravel\Response;

abstract class Controller {

	/**
	 * The "before" filters defined for the controller.
	 *
	 * @var array
	 */
	public $before = array();

	/**
	 * The "after" filters defined for the controller.
	 *
	 * @var array
	 */
	public $after = array();

	/**
	 * Handle the delegation of a route to a controller method.
	 *
	 * The controller destination should follow a {controller}@{method} convention.
	 * Nested controllers may be delegated to using dot syntax.
	 *
	 * For example, a destination of "user.profile@show" would call the User_Profile
	 * controller's show method with the given parameters.
	 *
	 * @param  string  $destination
	 * @param  array   $parameters
	 * @return mixed
	 */
	public static function call($destination, $parameters = array())
	{
		if (strpos($destination, '@') === false)
		{
			throw new \Exception("Route delegate [{$destination}] has an invalid format.");
		}

		list($controller, $method) = explode('@', $destination);

		$controller = static::resolve($controller);

		if (is_null($controller) or static::hidden($method))
		{
			return Response::error('404');
		}

		// Again, as was the case with route closures, if the controller
		// "before" filters return a response, it will be considered the
		// response to the request and the controller method will not be
		// used to handle the request to the application.
		$response = Filter::run($controller->filters('before'), array(), true);

		if (is_null($response))
		{
			$response = call_user_func_array(array($controller, $method), $parameters);
		}

		// The after filter and the framework expects all responses to
		// be instances of the Response class. If the method did not
		// return an instsance of Response, we will make on now.
		if ( ! $response instanceof Response) $response = new Response($response);

		Filter::run($controller->filters('after'), array($response));

		return $response;
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

		// If the controller is registered in the IoC container, we will
		// resolve it out of the container. Using constructor injection
		// on controllers via the container allows more flexible and
		// testable development of applications.
		if (IoC::container()->registered('controllers.'.$controller))
		{
			return IoC::container()->resolve('controllers.'.$controller);
		}

		$controller = str_replace(' ', '_', ucwords(str_replace('.', ' ', $controller))).'_Controller';

		return new $controller;
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
			require $path;

			return true;
		}

		return false;
	}

	/**
	 * Determine if a given controller method is callable.
	 *
	 * @param  string  $method
	 * @return bool
	 */
	protected static function hidden($method)
	{
		return $method == 'before' or $method == 'after' or strncmp($method, '_', 1) == 0;
	}

	/**
	 * Get an array of filter names defined for the destination.
	 *
	 * @param  string  $name
	 * @return array
	 */
	public function filters($name)
	{
		return (array) $this->$name;
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
	 *		$mailer = IoC::container()->resolve('mailer');
	 * </code>
	 */
	public function __get($key)
	{
		if (IoC::container()->registered($key))
		{
			return IoC::container()->resolve($key);
		}

		throw new \Exception("Attempting to access undefined property [$key] on controller.");
	}

}