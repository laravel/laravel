<?php namespace Laravel\Routing;

use Closure;
use Laravel\Bundle;
use Laravel\Response;

class Route {

	/**
	 * The route key, including request method and URI.
	 *
	 * @var string
	 */
	public $key;

	/**
	 * The URI the route responds to.
	 *
	 * @var string
	 */
	public $uris;

	/**
	 * The bundle in which the route was registered.
	 *
	 * @var string
	 */
	public $bundle;

	/**
	 * The action that is assigned to the route.
	 *
	 * @var mixed
	 */
	public $action;

	/**
	 * The parameters that will passed to the route callback.
	 *
	 * @var array
	 */
	public $parameters;

	/**
	 * Create a new Route instance.
	 *
	 * @param  string   $key
	 * @param  array    $action
	 * @param  array    $parameters
	 * @return void
	 */
	public function __construct($key, $action, $parameters = array())
	{
		$this->key = $key;
		$this->action = $action;
		$this->parameters = $parameters;

		// Extract each URI from the route key. Since the route key has the request
		// method, we will extract that from the string. If the URI points to the
		// root of the application, a single forward slash will be returned.
		$uris = array_get($action, 'handles', array($key));

		$this->uris = array_map(array($this, 'extract'), $uris);

		// Determine the bundle in which the route was registered. We will know
		// the bundle by the first segment of the route's URI. We need to know
		// the bundle so we know if we need to run a bundle's global filters
		// when executing the route.
		$this->bundle = Bundle::resolve(head(explode('/', $this->uris[0])));
	}

	/**
	 * Retrieve the URI from a given route destination.
	 *
	 * If the request is to the application root, a slash is returned.
	 *
	 * @param  string  $segment
	 * @return string
	 */
	protected static function extract($segment)
	{
		$uri = substr($segment, strpos($segment, ' ') + 1);

		return ($uri !== '/') ? trim($uri, '/') : $uri;
	}

	/**
	 * Call a given route and return the route's response.
	 *
	 * @return Response
	 */
	public function call()
	{
		// The route is responsible for running the global filters, and any
		// filters defined on the route itself. Since all incoming requests
		// come through a route (either defined or ad-hoc), it makes sense
		// to let the route handle the global filters.
		$response = Filter::run($this->filters('before'), array(), true);

		if (is_null($response))
		{
			$response = $this->response();
		}

		$response = Response::prepare($response);

		Filter::run($this->filters('after'), array($response));

		return $response;
	}

	/**
	 * Execute the route action and return the response.
	 *
	 * Unlike the "call" method, none of the attached filters will be run.
	 *
	 * @return mixed
	 */
	public function response()
	{
		// If the action is a string, it is simply pointing the route to a 
		// controller action, and we can just call the action and return
		// its response. This is the most basic form of route, and is
		// the simplest to handle.
		if ( ! is_null($delegate = $this->delegate()))
		{
			return Controller::call($delegate, $this->parameters);
		}

		// If the route does not have a delegate, it should either be a
		// Closure instance or have a Closure in its action array, so
		// we will attempt to get the Closure and call it.
		elseif ( ! is_null($handler = $this->handler()))
		{
			return call_user_func_array($handler, $this->parameters);
		}
	}

	/**
	 * Get the filters that are attached to the route for a given event.
	 *
	 * If the route belongs to a bundle, the bundle's global filters are returned too.
	 *
	 * @param  string  $filter
	 * @return array
	 */
	protected function filters($event)
	{
		// Add the global filters to the array. We will also attempt to add
		// the bundle's global filter as well. However, we'll need to keep
		// the array unique since the default bundle's global filter will
		// be the same as the application's global filter.
		$filters = array_unique(array($event, Bundle::prefix($this->bundle).$event));

		// Next wee will check to see if there are any filters attached
		// for the given event. If there are, we'll merge them in with
		// the global filters for the application event.
		if (isset($this->action[$event]))
		{
			$filters = array_merge($filters, Filter::parse($this->action[$event]));
		}

		return array(new Filter_Collection($filters));
	}

	/**
	 * Get the controller action delegate assigned to the route.
	 *
	 * If no delegate is assigned, null will be returned by the method.
	 *
	 * @return string
	 */
	protected function delegate()
	{
		return array_get($this->action, 'uses');
	}

	/**
	 * Get the anonymous function assigned to handle the route.
	 *
	 * If no anonymous function is assigned, null will be returned by the method.
	 *
	 * @return Closure
	 */
	protected function handler()
	{
		return array_first($this->action, function($key, $value)
		{
			return $value instanceof Closure;
		});
	}

	/**
	 * Determine if the route has a given name.
	 *
	 * <code>
	 *		// Determine if the route is the "login" route
	 *		$login = Request::route()->is('login');
	 * </code>
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public function is($name)
	{
		return is_array($this->action) and array_get($this->action, 'name') === $name;
	}

	/**
	 * Determine if the route handles a given URI.
	 *
	 * @param  string  $uri
	 * @return bool
	 */
	public function handles($uri)
	{
		$pattern = '#'.str_replace('*', '(.*)', $uri).'#';

		return ! is_null(array_first($this->uris, function($key, $uri) use ($pattern)
		{
			return preg_match($pattern, $uri);
		}));
	}

}