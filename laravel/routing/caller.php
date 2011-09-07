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
	 * The route filterer instance.
	 *
	 * @var Filterer
	 */
	protected $filterer;

	/**
	 * The route delegator instance.
	 *
	 * @var Delegator
	 */
	protected $delegator;

	/**
	 * Create a new route caller instance.
	 *
	 * @param  Container  $container
	 * @param  Filterer   $filterer
	 * @param  Delegator  $delegator
	 * @return void
	 */
	public function __construct(Container $container, Filterer $filterer, Delegator $delegator)
	{
		$this->filterer = $filterer;
		$this->container = $container;
		$this->delegator = $delegator;
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

		if ( ! is_null($response = $this->before($route)))
		{
			return $this->finish($route, $response);
		}

		if ( ! is_null($response = $route->call()))
		{
			if (is_array($response)) $response = $this->delegator->delegate($route, $response);

			return $this->finish($route, $response);
		}

		return $this->finish($route, $this->container->response->error('404'));
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

		return $this->filterer->filter($before, array(), true);
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

		$this->filterer->filter(array_merge($route->filters('after'), array('after')), array($response));

		return $response;
	}

}