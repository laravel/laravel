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

		// Extract each URI from the route key. Since the route key has the request
		// method, we will extract that from the string. If the URI points to the
		// root of the application, a single forward slash is returned.
		$uris = array_get($action, 'handles', array($key));

		$this->uris = array_map(array($this, 'destination'), $uris);

		// Determine the bundle in which the route was registered. We will know
		// the bundle by using the bundle::handles method, which will return
		// the bundle assigned to that URI.
		$this->bundle = Bundle::handles($this->uris[0]);

		$defaults = array_get($action, 'defaults', array());

		$this->parameters = array_merge($parameters, $defaults);

		// Once we have set the parameters and URIs, we'll transpose the route
		// parameters onto the URIs so that the routes response naturally to
		// the handles without the wildcards messing them up.
		foreach ($this->uris as &$uri)
		{
			$uri = $this->transpose($uri, $this->parameters);
		}
	}

	/**
	 * Substitute the parameters in a given URI.
	 *
	 * @param  string  $uri
	 * @param  array   $parameters
	 * @return string
	 */
	public static function transpose($uri, $parameters)
	{
		// Spin through each route parameter and replace the route wildcard segment
		// with the corresponding parameter passed to the method. Afterwards, we'll
		// replace all of the remaining optional URI segments.
		foreach ((array) $parameters as $parameter)
		{
			if ( ! is_null($parameter))
			{
				$uri = preg_replace('/\(.+?\)/', $parameter, $uri, 1);
			}
		}

		// If there are any remaining optional place-holders, we'll just replace
		// them with empty strings since not every optional parameter has to be
		// in the array of parameters that were passed.
		return str_replace(array_keys(Router::$optional), '', $uri);		
	}

	/**
	 * Call a given route and return the route's response.
	 *
	 * @return Response
	 */
	public function call()
	{
		// The route is responsible for running the global filters, and any
		// filters defined on the route itself, since all incoming requests
		// come through a route (either defined or ad-hoc).
		$response = Filter::run($this->filters('before'), array(), true);

		if (is_null($response))
		{
			$response = $this->response();
		}

		// We always return a Response instance from the route calls, so
		// we'll use the prepare method on the Response class to make
		// sure we have a valid Response isntance.
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
	 * @param  string  $event
	 * @return array
	 */
	protected function filters($event)
	{
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
		$pattern = ($uri !== '/') ? str_replace('*', '(.*)', $uri).'\z' : '^/$';

		return ! is_null(array_first($this->uris, function($key, $uri) use ($pattern)
		{
			return preg_match('#'.$pattern.'#', $uri);
		}));
	}

	/**
	 * Register a route with the router.
	 *
	 * <code>
	 *		// Register a route with the router
	 *		Router::register('GET /', function() {return 'Home!';});
	 *
	 *		// Register a route that handles multiple URIs with the router
	 *		Router::register(array('GET /', 'GET /home'), function() {return 'Home!';});
	 * </code>
	 *
	 * @param  string|array  $route
	 * @param  mixed         $action
	 * @param  bool          $https
	 * @return void
	 */
	public static function to($route, $action)
	{
		Router::register($route, $action);
	}

	/**
	 * Register a HTTPS route with the router.
	 *
	 * @param  string|array  $route
	 * @param  mixed         $action
	 * @return void
	 */
	public static function secure($route, $action)
	{
		Router::secure($route, $action);
	}

	/**
	 * Extract the URI string from a route destination.
	 *
	 * <code>
	 *		// Returns "home/index" as the destination's URI
	 *		$uri = Route::uri('GET /home/index');
	 * </code>
	 *
	 * @param  string  $destination
	 * @return string
	 */
	public static function destination($destination)
	{
		return trim(substr($destination, strpos($destination, '/')), '/') ?: '/';
	}

}