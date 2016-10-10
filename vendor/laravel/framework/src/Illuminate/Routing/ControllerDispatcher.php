<?php namespace Illuminate\Routing;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Container\Container;

class ControllerDispatcher {

	/**
	 * The routing filterer implementation.
	 *
	 * @var \Illuminate\Routing\RouteFiltererInterface  $filterer
	 */
	protected $filterer;

	/**
	 * The IoC container instance.
	 *
	 * @var \Illuminate\Container\Container
	 */
	protected $container;

	/**
	 * Create a new controller dispatcher instance.
	 *
	 * @param  \Illuminate\Routing\RouteFiltererInterface  $filterer
	 * @param  \Illuminate\Container\Container  $container
	 * @return void
	 */
	public function __construct(RouteFiltererInterface $filterer,
								Container $container = null)
	{
		$this->filterer = $filterer;
		$this->container = $container;
	}

	/**
	 * Dispatch a request to a given controller and method.
	 *
	 * @param  \Illuminate\Routing\Route  $route
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $controller
	 * @param  string  $method
	 * @return mixed
	 */
	public function dispatch(Route $route, Request $request, $controller, $method)
	{
		// First we will make an instance of this controller via the IoC container instance
		// so that we can call the methods on it. We will also apply any "after" filters
		// to the route so that they will be run by the routers after this processing.
		$instance = $this->makeController($controller);

		$this->assignAfter($instance, $route, $request, $method);

		$response = $this->before($instance, $route, $request, $method);

		// If no before filters returned a response we'll call the method on the controller
		// to get the response to be returned to the router. We will then return it back
		// out for processing by this router and the after filters can be called then.
		if (is_null($response))
		{
			$response = $this->call($instance, $route, $method);
		}

		return $response;
	}

	/**
	 * Make a controller instance via the IoC container.
	 *
	 * @param  string  $controller
	 * @return mixed
	 */
	protected function makeController($controller)
	{
		Controller::setFilterer($this->filterer);

		return $this->container->make($controller);
	}

	/**
	 * Call the given controller instance method.
	 *
	 * @param  \Illuminate\Routing\Controller  $instance
	 * @param  \Illuminate\Routing\Route  $route
	 * @param  string  $method
	 * @return mixed
	 */
	protected function call($instance, $route, $method)
	{
		$parameters = $route->parametersWithoutNulls();

		return $instance->callAction($method, $parameters);
	}

	/**
	 * Call the "before" filters for the controller.
	 *
	 * @param  \Illuminate\Routing\Controller  $instance
	 * @param  \Illuminate\Routing\Route  $route
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $method
	 * @return mixed
	 */
	protected function before($instance, $route, $request, $method)
	{
		foreach ($instance->getBeforeFilters() as $filter)
		{
			if ($this->filterApplies($filter, $request, $method))
			{
				// Here we will just check if the filter applies. If it does we will call the filter
				// and return the responses if it isn't null. If it is null, we will keep hitting
				// them until we get a response or are finished iterating through this filters.
				$response = $this->callFilter($filter, $route, $request);

				if ( ! is_null($response)) return $response;
			}
		}
	}

	/**
	 * Apply the applicable after filters to the route.
	 *
	 * @param  \Illuminate\Routing\Controller  $instance
	 * @param  \Illuminate\Routing\Route  $route
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $method
	 * @return mixed
	 */
	protected function assignAfter($instance, $route, $request, $method)
	{
		foreach ($instance->getAfterFilters() as $filter)
		{
			// If the filter applies, we will add it to the route, since it has already been
			// registered on the filterer by the controller, and will just let the normal
			// router take care of calling these filters so we do not duplicate logics.
			if ($this->filterApplies($filter, $request, $method))
			{
				$route->after($this->getAssignableAfter($filter));
			}
		}
	}

	/**
	 * Get the assignable after filter for the route.
	 *
	 * @param  Closure|string  $filter
	 * @return string
	 */
	protected function getAssignableAfter($filter)
	{
		return $filter['original'] instanceof Closure ? $filter['filter'] : $filter['original'];
	}

	/**
	 * Determine if the given filter applies to the request.
	 *
	 * @param  array  $filter
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $method
	 * @return bool
	 */
	protected function filterApplies($filter, $request, $method)
	{
		foreach (array('Only', 'Except', 'On') as $type)
		{
			if ($this->{"filterFails{$type}"}($filter, $request, $method))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Determine if the filter fails the "only" constraint.
	 *
	 * @param  array  $filter
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $method
	 * @return bool
	 */
	protected function filterFailsOnly($filter, $request, $method)
	{
		if ( ! isset($filter['options']['only'])) return false;

		return ! in_array($method, (array) $filter['options']['only']);
	}

	/**
	 * Determine if the filter fails the "except" constraint.
	 *
	 * @param  array  $filter
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $method
	 * @return bool
	 */
	protected function filterFailsExcept($filter, $request, $method)
	{
		if ( ! isset($filter['options']['except'])) return false;

		return in_array($method, (array) $filter['options']['except']);
	}

	/**
	 * Determine if the filter fails the "on" constraint.
	 *
	 * @param  array  $filter
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string  $method
	 * @return bool
	 */
	protected function filterFailsOn($filter, $request, $method)
	{
		$on = array_get($filter, 'options.on', null);

		if (is_null($on)) return false;

		// If the "on" is a string, we will explode it on the pipe so you can set any
		// amount of methods on the filter constraints and it will still work like
		// you specified an array. Then we will check if the method is in array.
		if (is_string($on)) $on = explode('|', $on);

		return ! in_array(strtolower($request->getMethod()), $on);
	}

	/**
	 * Call the given controller filter method.
	 *
	 * @param  array  $filter
	 * @param  \Illuminate\Routing\Route  $route
	 * @param  \Illuminate\Http\Request  $request
	 * @return mixed
	 */
	protected function callFilter($filter, $route, $request)
	{
		extract($filter);

		return $this->filterer->callRouteFilter($filter, $parameters, $route, $request);
	}

}
