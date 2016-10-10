<?php namespace Illuminate\Routing;

interface RouteFiltererInterface {

	/**
	 * Register a new filter with the router.
	 *
	 * @param  string  $name
	 * @param  mixed  $callback
	 * @return void
	 */
	public function filter($name, $callback);

	/**
	 * Call the given route filter.
	 *
	 * @param  string  $filter
	 * @param  array  $parameters
	 * @param  \Illuminate\Routing\Route  $route
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Illuminate\Http\Response|null $response
	 * @return mixed
	 */
	public function callRouteFilter($filter, $parameters, $route, $request, $response = null);

}
