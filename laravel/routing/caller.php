<?php namespace Laravel\Routing;

use Closure;
use Laravel\Response;
use Laravel\Container;

class Delegate {

	/**
	 * The destination of the route delegate.
	 *
	 * @var string
	 */
	public $destination;

	/**
	 * Create a new route delegate instance.
	 *
	 * @param  string  $destination
	 * @return void
	 */
	public function __construct($destination)
	{
		$this->destination = $destination;
	}

}

class Caller {

	/**
	 * The IoC container instance.
	 *
	 * @var Container
	 */
	protected $container;

	/**
	 * The route filters defined for the application.
	 *
	 * @var array
	 */
	protected $filters;

	/**
	 * The path to the application's controllers.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Create a new route caller instance.
	 *
	 * @param  Container  $container
	 * @param  Delegator  $delegator
	 * @param  array      $filters
	 * @return void
	 */
	public function __construct(Container $container, $filters, $path)
	{
		$this->path = $path;
		$this->filters = $filters;
		$this->container = $container;
	}

	/**
	 * Call a given route and return the route's response.
	 *
	 * @param  Route        $route
	 * @return Response
	 */
	public function call(Route $route)
	{
		// Since "before" filters can halt the request cycle, we will return any response
		// from the before filters. Allowing the filters to halt the request cycle makes
		// common tasks like authorization convenient to implement.
		if ( ! is_null($response = $this->before($route)))
		{
			return $this->finish($route, $response);
		}

		if ( ! is_null($response = $route->call()))
		{
			// If a route returns a Delegate, it also means the route is delegating the
			// handling of the request to a controller method. So, we will pass the string
			// to the route delegator, exploding on "@".
			if ($response instanceof Delegate) $response = $this->delegate($route, $response->destination);

			return $this->finish($route, $response);
		}

		// If we get to this point, no response was returned from the filters or the route.
		// The 404 response will be returned to the browser instead of a blank screen.
		return $this->finish($route, $this->container->resolve('laravel.response')->error('404'));
	}

	/**
	 * Run the "before" filters for the route.
	 *
	 * If a before filter returns a value, that value will be considered the response to the
	 * request and the route function / controller will not be used to handle the request.
	 *
	 * @param  Route  $route
	 * @return mixed
	 */
	protected function before(Route $route)
	{
		$before = array_merge(array('before'), $route->filters('before'));

		return $this->filter($before, array(), true);
	}

	/**
	 * Handle the delegation of a route to a controller method.
	 *
	 * @param  Route   $route
	 * @param  string  $delegate
	 * @return mixed
	 */
	protected function delegate(Route $route, $delegate)
	{
		if (strpos($delegate, '@') === false)
		{
			throw new \Exception("Route delegate [$delegate] has an invalid format.");
		}

		list($controller, $method) = explode('@', $delegate);

		$controller = $this->resolve($controller);

		// If the controller doesn't exist or the request is to an invalid method, we will
		// return the 404 error response. The "before" method and any method beginning with
		// an underscore are not publicly available.
		if (is_null($controller) or ($method == 'before' or strncmp($method, '_', 1) === 0))
		{
			return Response::error('404');
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

		// If the controller is registered in the IoC container, we will resolve it out
		// of the container. Using constructor injection on controllers via the container
		// allows more flexible and testable development of applications.
		if ($this->container->registered('controllers.'.$controller))
		{
			return $this->container->resolve('controllers.'.$controller);
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
	 * Finish the route handling for the request.
	 *
	 * The route response will be converted to a Response instance and the "after" filters will be run.
	 *
	 * @param  Route        $route
	 * @param  mixed        $response
	 * @return Response
	 */
	protected function finish(Route $route, $response)
	{
		if ( ! $response instanceof Response) $response = new Response($response);

		$this->filter(array_merge($route->filters('after'), array('after')), array($response));

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
			// Parameters may be passed into routes by specifying the list of parameters after
			// a colon. If parameters are present, we will merge them into the parameter array
			// that was passed to the method and slice the parameters off of the filter string.
			if (($colon = strpos($filter, ':')) !== false)
			{
				$parameters = array_merge($parameters, explode(',', substr($filter, $colon + 1)));

				$filter = substr($filter, 0, $colon);
			}

			if ( ! isset($this->filters[$filter])) continue;

			$response = call_user_func_array($this->filters[$filter], $parameters);

			// "Before" filters may override the request cycle. For example, an authentication
			// filter may redirect a user to a login view if they are not logged in. Because of
			// this, we will return the first filter response if overriding is enabled.
			if ( ! is_null($response) and $override) return $response;
		}
	}

}