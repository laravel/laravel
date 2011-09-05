<?php namespace Laravel\Routing;

use Closure;
use Laravel\Response;
use Laravel\Container;

class Caller {

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
	protected $controller_path;

	/**
	 * Create a new route caller instance.
	 *
	 * @param  Container  $container
	 * @param  string     $controller_path
	 * @return void
	 */
	public function __construct(Container $container, $controller_path)
	{
		$this->container = $container;
		$this->controller_path = $controller_path;
	}

	/**
	 * Call a given route and return the route's response.
	 *
	 * @param  Route        $route
	 * @return Response
	 */
	public function call(Route $route)
	{
		if ( ! $route->callback instanceof \Closure and ! is_array($route->callback))
		{
			throw new \Exception('Invalid route defined for URI ['.$route->key.']');
		}

		// Run the "before" filters for the route. If a before filter returns a value, that value
		// will be considered the response to the request and the route function / controller will
		// not be used to handle the request.
		$before = array_merge($route->before(), array('before'));

		if ( ! is_null($response = $this->filter($route, $before, array(), true)))
		{
			return $this->finish($route, $response);
		}

		$closure = ( ! $route->callback instanceof Closure) ? $this->find_route_closure($route) : $route->callback;

		if ( ! is_null($closure)) return $this->handle_closure($route, $closure);

		return $this->finish($route, $this->container->resolve('laravel.response')->error('404'));
	}

	/**
	 * Extract the route closure from the route.
	 *
	 * If a "do" index is specified on the callback, that is the handler.
	 * Otherwise, we will return the first callable array value.
	 *
	 * @param  Route    $route
	 * @return Closure
	 */
	protected function find_route_closure(Route $route)
	{
		if (isset($route->callback['do'])) return $route->callback['do'];

		foreach ($route->callback as $value) { if ($value instanceof Closure) return $value; }
	}

	/**
	 * Handle a route closure.
	 *
	 * @param  Route        $route
	 * @param  Closure      $closure
	 * @return mixed
	 */
	protected function handle_closure(Route $route, Closure $closure)
	{
		$response = call_user_func_array($closure, $route->parameters);

		// If the route closure returns an array, we assume that they are returning a
		// reference to a controller and method and will use the given controller method
		// to handle the request to the application.
		if (is_array($response))
		{
			$response = $this->delegate($route, $response[0], $response[1], $route->parameters);
		}

		return $this->finish($route, $response);
	}

	/**
	 * Handle the delegation of a route to a controller method.
	 *
	 * @param  Route        $route
	 * @param  string       $controller
	 * @param  string       $method
	 * @param  array        $parameters
	 * @return Response
	 */
	protected function delegate(Route $route, $controller, $method, $parameters)
	{
		if ( ! file_exists($path = $this->controller_path.strtolower(str_replace('.', '/', $controller)).EXT))
		{
			throw new \Exception("Controller [$controller] does not exist.");
		}

		require $path;

		$controller = $this->resolve_controller($controller);

		if ($method == 'before' or strncmp($method, '_', 1) === 0)
		{
			$response = $this->container->resolve('laravel.response')->error('404');
		}
		else
		{
			$response = $controller->before();
		}

		// Again, as was the case with route closures, if the controller "before" method returns
		// a response, it will be considered the response to the request and the controller method
		// will not be used to handle the request to the application.
		return (is_null($response)) ? call_user_func_array(array($controller, $method), $parameters) : $response;
	}

	/**
	 * Resolve a controller name to a controller instance.
	 *
	 * @param  string      $controller
	 * @return Controller
	 */
	protected function resolve_controller($controller)
	{
		if ($this->container->registered('controllers.'.$controller)) return $this->container->resolve('controllers.'.$controller);

		$controller = str_replace(' ', '_', ucwords(str_replace('.', ' ', $controller))).'_Controller';

		return new $controller;
	}

	/**
	 * Finish the route handling for the request.
	 *
	 * The route response will be converted to a Response instance and the "after" filters
	 * defined for the route will be executed.
	 *
	 * @param  Route        $route
	 * @param  mixed        $response
	 * @return Response
	 */
	protected function finish(Route $route, $response)
	{
		if ( ! $response instanceof Response) $response = new Response($response);

		$this->filter($route, array_merge($route->after(), array('after')), array($response));

		return $response;
	}

	/**
	 * Call a filter or set of filters.
	 *
	 * @param  Route  $route
	 * @param  array  $filters
	 * @param  array  $parameters
	 * @param  bool   $override
	 * @return mixed
	 */
	protected function filter(Route $route, $filters, $parameters = array(), $override = false)
	{
		foreach ((array) $filters as $filter)
		{
			if ( ! isset($route->filters[$filter])) continue;

			$response = call_user_func_array($route->filters[$filter], $parameters);

			// "Before" filters may override the request cycle. For example, an authentication
			// filter may redirect a user to a login view if they are not logged in. Because of
			// this, we will return the first filter response if overriding is enabled.
			if ( ! is_null($response) and $override) return $response;
		}
	}

}