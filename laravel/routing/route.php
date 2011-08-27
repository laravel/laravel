<?php namespace Laravel\Routing;

use Closure;
use Laravel\Response;
use Laravel\Container;
use Laravel\Application;

class Route {

	/**
	 * The route key, including request method and URI.
	 *
	 * @var string
	 */
	public $key;

	/**
	 * The route callback or array.
	 *
	 * @var mixed
	 */
	public $callback;

	/**
	 * The parameters that will passed to the route function.
	 *
	 * @var array
	 */
	public $parameters;

	/**
	 * The route filters for the application.
	 *
	 * @param  array  $filters
	 */
	public $filters = array();

	/**
	 * The path the application controllers.
	 *
	 * @var string
	 */
	protected $controller_path;

	/**
	 * Create a new Route instance.
	 *
	 * @param  string   $key
	 * @param  mixed    $callback
	 * @param  array    $parameters
	 * @return void
	 */
	public function __construct($key, $callback, $parameters, $controller_path)
	{
		$this->key = $key;
		$this->callback = $callback;
		$this->parameters = $parameters;
		$this->controller_path = $controller_path;
	}

	/**
	 * Execute the route for a given request to the application and return the response.
	 *
	 * @param  Application  $application
	 * @return Response
	 */
	public function call(Application $application)
	{
		$this->validate();

		if ( ! is_null($response = $this->filter(array_merge($this->before(), array('before')), array($application), true)))
		{
			return $this->finish($application, $response);
		}

		$closure = ( ! $this->callback instanceof Closure) ? $this->find_route_closure() : $this->callback;

		if ( ! is_null($closure)) return $this->handle_closure($application, $closure);

		return $this->finish($application, $application->responder->error('404'));
	}

	/**
	 * Validate that a given route is callable.
	 *
	 * @return void
	 */
	protected function validate()
	{
		if ( ! $this->callback instanceof Closure and ! is_array($this->callback))
		{
			throw new \Exception('Invalid route defined for URI ['.$this->key.']');
		}
	}

	/**
	 * Extract the route closure from the route.
	 *
	 * If a "do" index is specified on the callback, that is the handler.
	 * Otherwise, we will return the first callable array value.
	 *
	 * @return Closure
	 */
	protected function find_route_closure()
	{
		if (isset($this->callback['do'])) return $this->callback['do'];

		foreach ($this->callback as $value) { if ($value instanceof Closure) return $value; }
	}

	/**
	 * Handle a route closure.
	 *
	 * @param  Route    $route
	 * @param  Closure  $closure
	 * @return mixed
	 */
	protected function handle_closure(Application $application, Closure $closure)
	{
		array_unshift($this->parameters, $application);

		$response = call_user_func_array($closure, $this->parameters);

		if (is_array($response))
		{
			$response = $this->delegate($application, $response[0], $response[1], $this->parameters);
		}

		return $this->finish($application, $response);
	}

	/**
	 * Handle the delegation of a route to a controller method.
	 *
	 * @param  Application  $application
	 * @param  string       $controller
	 * @param  string       $method
	 * @param  array        $parameters
	 * @return Response
	 */
	protected function delegate(Application $application, $controller, $method, $parameters)
	{
		if ( ! file_exists($path = $this->controller_path.strtolower(str_replace('.', '/', $controller)).EXT))
		{
			throw new \Exception("Controller [$controller] is not defined.");
		}

		require $path;

		$controller = $this->resolve($application->container, $controller);

		if ($method == 'before' or strncmp($method, '_', 1) === 0)
		{
			$response = $application->responder->error('404');
		}
		else
		{
			$response = $controller->before();
		}

		return (is_null($response)) ? call_user_func_array(array($controller, $method), $parameters) : $response;
	}

	/**
	 * Resolve a controller name to a controller instance.
	 *
	 * @param  Container   $container
	 * @param  string      $controller
	 * @return Controller
	 */
	protected function resolve(Container $container, $controller)
	{
		if ($container->registered('controllers.'.$controller))
		{
			return $container->resolve('controllers.'.$controller);
		}

		$controller = str_replace(' ', '_', ucwords(str_replace('.', ' ', $controller))).'_Controller';

		return new $controller;
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
	protected function finish(Application $application, $response)
	{
		if ( ! $response instanceof Response) $response = new Response($response);

		$this->filter(array_merge($this->after(), array('after')), array($application, $response));

		return $response;
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
	 * Get all of the "before" filters defined for the route.
	 *
	 * @return array
	 */
	public function before()
	{
		return $this->filters('before');
	}

	/**
	 * Get all of the "after" filters defined for the route.
	 *
	 * @return array
	 */
	public function after()
	{
		return $this->filters('after');
	}

	/**
	 * Get an array of filters defined for the route.
	 *
	 * <code>
	 *		// Get all of the "before" filters defined for the route.
	 *		$filters = $route->filters('before');
	 * </code>
	 *
	 * @param  string  $name
	 * @return array
	 */
	private function filters($name)
	{
		return (is_array($this->callback) and isset($this->callback[$name])) ? explode(', ', $this->callback[$name]) : array();
	}

}