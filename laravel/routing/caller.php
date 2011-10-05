<?php namespace Laravel\Routing;

use Closure;
use Laravel\Response;
use Laravel\Container;
use Laravel\Controller;

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
	 * @param  Route     $route
	 * @return Response
	 */
	public function call(Route $route)
	{
		// Since "before" filters can halt the request cycle, we will return any response
		// from the before filters. Allowing the filters to halt the request cycle makes
		// common tasks like authorization convenient to implement.
		$before = array_merge(array('before'), $route->filters('before'));

		if ( ! is_null($response = $this->filter($before, array(), true)))
		{
			return $this->finish($route, $response);
		}

		// If a route returns a Delegate, it means the route is delegating the handling
		// of the request to a controller method. We will pass the Delegate instance
		// to the "delegate" method which will call the controller.
		if ($route->delegates())
		{
			return $this->delegate($route, $route->call());
		}
		// If no before filters returned a response and the route is not delegating
		// execution to a controller, we will call the route like normal and return
		// the response. If the no response is given by the route, we will return
		// the 404 error view.
		elseif ( ! is_null($response = $route->call()))
		{
			return $this->finish($route, $response);
		}
		else
		{
			return $this->finish($route, Response::error('404'));
		}
	}

	/**
	 * Handle the delegation of a route to a controller method.
	 *
	 * @param  Route     $route
	 * @param  Delegate  $delegate
	 * @return mixed
	 */
	protected function delegate(Route $route, Delegate $delegate)
	{
		// Route delegates follow a {controller}@{method} naming convention. For example,
		// to delegate to the "home" controller's "index" method, the delegate should be
		// formatted like "home@index". Nested controllers may be delegated to using dot
		// syntax, like so: "user.profile@show".
		if (strpos($delegate->destination, '@') === false)
		{
			throw new \Exception("Route delegate [{$delegate->destination}] has an invalid format.");
		}

		list($controller, $method) = explode('@', $delegate->destination);

		$controller = Controller::resolve($this->container, $controller, $this->path);

		// If the controller doesn't exist or the request is to an invalid method, we will
		// return the 404 error response. The "before" method and any method beginning with
		// an underscore are not publicly available.
		if (is_null($controller) or ! $this->callable($method))
		{
			return Response::error('404');
		}

		$controller->container = $this->container;

		// Again, as was the case with route closures, if the controller "before" filters
		// return a response, it will be considered the response to the request and the
		// controller method will not be used to handle the request to the application.
		$response = $this->filter($controller->filters('before'), array(), true);

		if (is_null($response))
		{
			$response = call_user_func_array(array($controller, $method), $route->parameters);
		}

		return $this->finish($controller, $response);
	}

	/**
	 * Determine if a given controller method is callable.
	 *
	 * @param  string  $method
	 * @return bool
	 */
	protected function callable($method)
	{
		return $method == 'before' or $method == 'after' or strncmp($method, '_', 1) === 0;
	}

	/**
	 * Finish the route handling for the request.
	 *
	 * The route response will be converted to a Response instance and the "after" filters will be run.
	 *
	 * @param  Route|Controller  $destination
	 * @param  mixed             $response
	 * @return Response
	 */
	protected function finish($destination, $response)
	{
		if ( ! $response instanceof Response) $response = new Response($response);

		$this->filter(array_merge($destination->filters('after'), array('after')), array($response));

		return $response;
	}

	/**
	 * Call a filter or set of filters.
	 *
	 * @param  array|string  $filters
	 * @param  array         $parameters
	 * @param  bool          $override
	 * @return mixed
	 */
	protected function filter($filters, $parameters = array(), $override = false)
	{
		if (is_string($filters)) $filters = explode('|', $filters);

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