<?php namespace Laravel\Routing;

use Closure;
use Laravel\IoC;
use Laravel\Error;
use Laravel\Request;
use Laravel\Response;

class Handler {

	/**
	 * The active request instance.
	 *
	 * @var Request
	 */
	protected $request;

	/**
	 * The route filter manager.
	 *
	 * @var array
	 */
	protected $filters;

	/**
	 * Create a new route handler instance.
	 *
	 * @param  array  $filters
	 * @return void
	 */
	public function __construct(Request $request, $filters)
	{
		$this->request = $request;
		$this->filters = $filters;
	}

	/**
	 * Execute a given route and return the response.
	 *
	 * @param  Route     $route
	 * @return Response
	 */
	public function handle(Route $route)
	{
		$this->validate($route);

		if ( ! is_null($response = $this->filter(array_merge($route->before(), array('before')), array($this->request), true)))
		{
			return $this->finish($route, $response);
		}

		$closure = ( ! $route->callback instanceof Closure) ? $this->find_route_closure($route) : $route->callback;

		if ( ! is_null($closure)) return $this->handle_closure($route, $closure);

		return $this->finish($route, new Error('404'));
	}

	/**
	 * Validate that a given route is callable.
	 *
	 * @param  Route  $route
	 * @return void
	 */
	protected function validate(Route $route)
	{
		if ( ! $route->callback instanceof Closure and ! is_array($route->callback))
		{
			throw new \Exception('Invalid route defined for URI ['.$route->key.']');
		}
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

		foreach ($route->callback as $value) { if (is_callable($value)) return $value; }
	}

	/**
	 * Handle a route closure.
	 *
	 * @param  Route    $route
	 * @param  Closure  $closure
	 * @return mixed
	 */
	protected function handle_closure(Route $route, Closure $closure)
	{
		$response = call_user_func_array($closure, $route->parameters);

		if (is_array($response))
		{
			$response = $this->delegate($response[0], $response[1], $route->parameters);
		}

		return $this->finish($route, $response);
	}

	/**
	 * Handle the delegation of a route to a controller method.
	 *
	 * @param  string    $controller
	 * @param  string    $method
	 * @param  array     $parameters
	 * @return Response
	 */
	protected function delegate($controller, $method, $parameters)
	{
		if ( ! file_exists($path = CONTROLLER_PATH.strtolower(str_replace('.', '/', $controller)).EXT))
		{
			throw new \Exception("Controller [$controller] is not defined.");
		}

		require $path;

		$controller = $this->resolve($controller);

		$response = $controller->before($this->request);

		return (is_null($response)) ? call_user_func_array(array($controller, $method), $parameters) : $response;
	}

	/**
	 * Resolve a controller name to a controller instance.
	 *
	 * @param  string      $controller
	 * @return Controller
	 */
	protected function resolve($controller)
	{
		if (IoC::container()->registered('controllers.'.$controller))
		{
			return IoC::container()->resolve('controllers.'.$controller);
		}

		$controller = str_replace(' ', '_', ucwords(str_replace('.', ' ', $controller))).'_Controller';

		return new $controller;
	}

	/**
	 * Call a filter or set of filters.
	 *
	 * @param  array  $filters
	 * @param  array  $parameters
	 * @param  bool   $override
	 * @return mixed
	 */
	protected function filter($filters, $parameters = array(), $override = false)
	{
		foreach ((array) $filters as $filter)
		{
			if ( ! isset($this->filters[$filter])) continue;

			$response = call_user_func_array($this->filters[$filter], $parameters);

			// "Before" filters may override the request cycle. For example, an authentication
			// filter may redirect a user to a login view if they are not logged in. Because of
			// this, we will return the first filter response if overriding is enabled.
			if ( ! is_null($response) and $override) return $response;
		}
	}

	/**
	 * Finish the route handling for the request.
	 *
	 * The route response will be converted to a Response instance and the "after" filters
	 * defined for the route will be executed.
	 *
	 * @param  Route     $route
	 * @param  mixed     $response
	 * @return Response
	 */
	protected function finish(Route $route, $response)
	{
		if ( ! $response instanceof Response) $response = new Response($response);

		$this->filter(array_merge($route->after(), array('after')), array($this->request, $response));

		return $response;
	}

}