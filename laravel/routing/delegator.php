<?php namespace Laravel\Routing;

use Laravel\Container;

class Delegator {

	/**
	 * The IoC container instance.
	 *
	 * @var Container
	 */
	protected $container;

	/**
	 * The path to the application controllers.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Create a new route delegator instance.
	 *
	 * @param  Container  $container
	 * @param  string     $path
	 * @return void
	 */
	public function __construct(Container $container, $path)
	{
		$this->path = $path;
		$this->container = $container;
	}

	/**
	 * Handle the delegation of a route to a controller method.
	 *
	 * @param  Route  $route
	 * @param  array  $delegate
	 * @return mixed
	 */
	public function delegate(Route $route, $delegate)
	{
		list($controller, $method) = array($delegate[0], $delegate[1]);

		$controller = $this->resolve($controller);

		// If the controller doesn't exist or the request is to an invalid method, we will
		// return the 404 error response. The "before" method and any method beginning with
		// an underscore are not publicly available.
		if (is_null($controller) or ($method == 'before' or strncmp($method, '_', 1) === 0))
		{
			return $this->container->resolve('laravel.response')->error('404');
		}

		$controller->container = $this->container;

		// Again, as was the case with route closures, if the controller "before" method returns
		// a response, it will be considered the response to the request and the controller method
		// will not be used to handle the request to the application.
		$response = $controller->before();

		return (is_null($response)) ? call_user_func_array(array($controller, $method), $route->parameters) : $response;
	}

	/**
	 * Resolve a controller name to a controller instance.
	 *
	 * @param  string      $controller
	 * @return Controller
	 */
	protected function resolve($controller)
	{
		if ( ! $this->load($controller)) return;

		if ($this->container->registered('controllers.'.$controller))
		{
			return $this->container->resolve('controllers.'.$controller);
		}

		$controller = $this->format($controller);

		return new $controller;
	}

	/**
	 * Load the file for a given controller.
	 *
	 * @param  string  $controller
	 * @return bool
	 */
	protected function load($controller)
	{
		if (file_exists($path = $this->path.strtolower(str_replace('.', '/', $controller)).EXT))
		{
			require $path;

			return true;
		}

		return false;
	}

	/**
	 * Format a controller name to its class name.
	 *
	 * All controllers are suffixed with "_Controller" to avoid namespacing. It gives the developer
	 * a more convenient environment since the controller sits in the global namespace.
	 *
	 * @param  string  $controller
	 * @return string
	 */
	protected function format($controller)
	{
		return str_replace(' ', '_', ucwords(str_replace('.', ' ', $controller))).'_Controller';
	}

}