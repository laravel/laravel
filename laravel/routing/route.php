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
	 * The URIs the route responds to.
	 *
	 * @var array
	 */
	public $uris;

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
	 * The bundle in which the route was registered.
	 *
	 * @var string
	 */
	public $bundle;

	/**
	 * Create a new Route instance.
	 *
	 * @param  string   $key
	 * @param  string   $action
	 * @param  array    $parameters
	 * @return void
	 */
	public function __construct($key, $action, $parameters = array())
	{
		$this->key = $key;
		$this->action = $action;
		$this->parameters = $parameters;

		// Extract each URI from the route key. Since the route key has the
		// request method, we will extract that from the string. If the URI
		// points to the root of the application, a single forward slash
		// will be returned since that is used for the root route.
		if (strpos($key, ', ') === false)
		{
			$this->uris = array(static::extract($this->key));
		}
		else
		{
			$this->uris = array_map(array($this, 'extract'), explode(', ', $key));
		}

		// Determine the bundle in which the route was registered. We will
		// know the bundle by the first segment of the route's URI. If it
		// matches a bundle name, that is the bundle in which we will
		// assume the route was registered.
		$this->bundle = Bundle::resolve(head(explode('/', $this->uris[0])));

		if ( ! static::callable($this->action))
		{
			throw new \Exception("Invalid route defined for URI [{$this->key}]");
		}
	}

	/**
	 * Determine if the given route action is callable.
	 *
	 * Route actions must be either a Closure, array, or string.
	 *
	 * @param  mixed  $action
	 * @return bool
	 */
	protected static function callable($action)
	{
		return $action instanceof Closure or is_array($action) or is_string($action);
	}

	/**
	 * Retrieve the URI from a given route destination.
	 *
	 * If the request is to the root of the application, a single slash is returned.
	 *
	 * @param  string  $segment
	 * @return string
	 */
	protected static function extract($segment)
	{
		$segment = substr($segment, strpos($segment, ' ') + 1);

		return ($segment !== '/') ? trim($segment, '/') : $segment;
	}

	/**
	 * Call a given route and return the route's response.
	 *
	 * @return Response
	 */
	public function call()
	{
		// Since "before" filters can halt the request cycle, we will return
		// any response from the before filters. Allowing filters to halt a
		// request cycle makes tasks like authorization convenient.
		//
		// The route is responsible for running the global filters, and any
		// filters defined on the route itself. Since all incoming requests
		// come through a route (either defined or ad-hoc), it makes sense
		// to let the route handle the global filters. If the route uses
		// a controller, the controller will only call its own filters.
		$response = Filter::run($this->filters('before'), array(), true);

		if (is_null($response))
		{
			$response = $this->response();
		}

		if ( ! $response instanceof Response)
		{
			$response = new Response($response);
		}

		// Stringify the response. We will need to force the response to be
		// stringed before closing the session, since the developer may be
		// using the session within their views, so we cannot age the
		// session data until the view is rendered.
		$response->content = (string) $response->content;

		Filter::run($this->filters('after'), array($response));

		return $response;
	}

	/**
	 * Execute the route action and return the response.
	 *
	 * Unlike the "execute" method, none of the attached filters will be run.
	 *
	 * @return mixed
	 */
	public function response()
	{
		// If the action is a string, it is simply pointing the route to a 
		// controller action, and we can just call the action and return
		// its response. This is the most basic form of route, and is
		// the simplest to handle.
		if (is_string($this->action))
		{
			return Controller::call($this->action, $this->parameters);
		}
		// If the action is a Closure, the route has provided an anonymous
		// function to handle the executino of the route. This provides
		// the developer with an extremely simply way to quickly build
		// APIs or simple applications. All we need to do is execute
		// the Closure and return the response.
		elseif ($this->action instanceof Closure)
		{
			return call_user_func_array($this->action, $this->parameters);
		}

		// If the action array contains a "uses" key, it means the route
		// is passing off execution to a controller. The only reason the
		// route is an array at all is probably because the developer 
		// attached filters to the route.
		if (isset($this->action['uses']))
		{
			return Controller::call($this->action['uses'], $this->parameters);
		}
		// If the action array contains a Closure callback, we will just
		// execute the call and return its response. This occurs when
		// the developer specifies an anonymous function to handle
		// the route, as well as route filters or a route name.
		else
		{
			$callback = array_first($this->action, function($key, $value)
			{
				return $value instanceof Closure;
			});

			if (is_null($callback)) return;

			return call_user_func_array($callback, $this->parameters);
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
		$filters = array_unique(array($event, Bundle::prefix($this->bundle).$event));

		// If the route action is an array, we'll check to see if any filters
		// are attached for the given event. If there are filters attached to
		// the given event, we'll merge them in with the global filters.
		if (is_array($this->action) and isset($this->action[$event]))
		{
			$filters = array_merge($filters, Filter_Collection::parse($this->action[$event]));
		}

		return array(new Filter_Collection($filters));
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
		return in_array($uri, $this->uris);
	}

}