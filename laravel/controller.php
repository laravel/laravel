<?php namespace Laravel;

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
	 * Get an array of filter names defined for the destination.
	 *
	 * @param  string  $name
	 * @return array
	 */
	public function filters($name)
	{
		return $this->$name;
	}

	/**
	 * Resolve a controller name to a controller instance.
	 *
	 * @param  Container   $container
	 * @param  string      $controller
	 * @param  string      $path
	 * @return Controller
	 */
	public static function resolve(Container $container, $controller, $path)
	{
		if ( ! static::load($controller, $path)) return;

		// If the controller is registered in the IoC container, we will resolve it out
		// of the container. Using constructor injection on controllers via the container
		// allows more flexible and testable development of applications.
		if ($container->registered('controllers.'.$controller))
		{
			return $container->resolve('controllers.'.$controller);
		}

		// If the controller was not registered in the container, we will instantiate
		// an instance of the controller manually. All controllers are suffixed with
		// "_Controller" to avoid namespacing. Allowing controllers to exist in the
		// global namespace gives the developer a convenient API for using the framework.
		$controller = str_replace(' ', '_', ucwords(str_replace('.', ' ', $controller))).'_Controller';

		return new $controller;
	}

	/**
	 * Load the file for a given controller.
	 *
	 * @param  string  $controller
	 * @param  string  $path
	 * @return bool
	 */
	protected static function load($controller, $path)
	{
		if (file_exists($path = $path.strtolower(str_replace('.', '/', $controller)).EXT))
		{
			require $path;

			return true;
		}

		return false;
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
		if (IoC::container()->registered($key)) return IoC::container()->resolve($key);

		throw new \Exception("Attempting to access undefined property [$key] on controller.");
	}

}