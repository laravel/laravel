<?php namespace Laravel\Routing;

use Closure;
use Laravel\Arr;
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
	 * The route callback or array.
	 *
	 * @var mixed
	 */
	public $callback;

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
	 * @param  mixed    $callback
	 * @param  array    $parameters
	 * @return void
	 */
	public function __construct($key, $callback, $parameters = array())
	{
		$this->key = $key;
		$this->callback = $callback;
		$this->parameters = $parameters;

		// Extract each URI from the route key. Since the route key has the
		// request method, we will extract that from the string. If the URI
		// points to the root of the application, a single forward slash
		// will be returned since that is used for the root route.
		if (strpos($key, ', ') === false)
		{
			$this->uris = array($this->extract($this->key));
		}
		else
		{
			$this->uris = array_map(array($this, 'extract'), explode(', ', $key));
		}

		if ( ! $this->callable($callback))
		{
			throw new \InvalidArgumentException('Invalid route defined for URI ['.$this->key.']');
		}
	}

	/**
	 * Determine if the given route callback is callable.
	 *
	 * Route callbacks must be either a Closure, array, or string.
	 *
	 * @param  mixed  $callback
	 * @return bool
	 */
	protected function callable($callback)
	{
		return $callback instanceof Closure or is_array($callback) or is_string($callback);
	}

	/**
	 * Retrieve the URI from a given route destination.
	 *
	 * If the request is to the root of the application, a single slash
	 * will be returned, otherwise the leading slash will be removed.
	 *
	 * @param  string  $segment
	 * @return string
	 */
	protected function extract($segment)
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
		// any response from the before filters. Allowing filters to halt the
		// request cycle makes tasks like authorization convenient.
		//
		// The route is responsible for running the global filters, and any
		// filters defined on the route itself. Since all incoming requests
		// come through a route (either defined or ad-hoc), it makes sense
		// to let the route handle the global filters. If the route uses
		// a controller, the controller will only call its own filters.
		$before = array_merge(array('before'), $this->filters('before'));

		$response = Filter::run($before, array(), true);

		if (is_null($response) and ! is_null($response = $this->response()))
		{
			if ($response instanceof Delegate)
			{
				$response = Controller::call($response->destination, $this->parameters);
			}
		}

		if ( ! $response instanceof Response)
		{
			$response = new Response($response);
		}

		// Stringify the response. We need to force the response to be
		// stringed before closing the session, since the developer may
		// be using the session within their views, so we cannot age
		// the session data until the view is rendered.
		$response->content = $response->render();

		$filters = array_merge($this->filters('after'), array('after'));

		Filter::run($filters, array($response));

		return $response;
	}

	/**
	 * Call the closure defined for the route, or get the route delegator.
	 *
	 * Note that this method differs from the "call" method in that it does
	 * not resolve the controller or prepare the response. Delegating to
	 * controller's is handled by the "call" method.
	 *
	 * @return mixed
	 */
	protected function response()
	{
		// If the route callback is an instance of a Closure, we can call the
		// route function directly. There are no before or after filters to
		// parse out of the route.
		if ($this->callback instanceof Closure)
		{
			return call_user_func_array($this->callback, $this->parameters);
		}
		// If the route is an array, we will return the first value with a
		// key of "uses", or the first instance of a Closure. If the value
		// is a string, the route is delegating the responsibility for
		// for handling the request to a controller.
		elseif (is_array($this->callback))
		{
			$callback = Arr::first($this->callback, function($key, $value)
			{
				return $key == 'uses' or $value instanceof Closure;
			});

			if ($callback instanceof Closure)
			{
				return call_user_func_array($callback, $this->parameters);
			}
			else
			{
				return new Delegate($callback);
			}
		}
		elseif (is_string($this->callback))
		{
			return new Delegate($this->callback);
		}
	}

	/**
	 * Get an array of filter names defined for the route.
	 *
	 * @param  string  $name
	 * @return array
	 */
	public function filters($name)
	{
		if (is_array($this->callback) and isset($this->callback[$name]))
		{
			$filters = $this->callback[$name];

			return (is_string($filters)) ? explode('|', $filters) : (array) $filters;
		}

		return array();
	}

	/**
	 * Determine if the route has a given name.
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public function is($name)
	{
		return is_array($this->callback) and Arr::get($this->callback, 'name') === $name;
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

	/**
	 * Magic Method to handle dynamic method calls to determine the name of the route.
	 */
	public function __call($method, $parameters)
	{
		if (strpos($method, 'is_') === 0)
		{
			return $this->is(substr($method, 3));
		}

		throw new \BadMethodCallException("Call to undefined method [$method] on Route class.");
	}

}