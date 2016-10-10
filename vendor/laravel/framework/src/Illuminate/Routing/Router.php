<?php namespace Illuminate\Routing;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Router implements HttpKernelInterface, RouteFiltererInterface {

	/**
	 * The event dispatcher instance.
	 *
	 * @var \Illuminate\Events\Dispatcher
	 */
	protected $events;

	/**
	 * The IoC container instance.
	 *
	 * @var \Illuminate\Container\Container
	 */
	protected $container;

	/**
	 * The route collection instance.
	 *
	 * @var \Illuminate\Routing\RouteCollection
	 */
	protected $routes;

	/**
	 * The currently dispatched route instance.
	 *
	 * @var \Illuminate\Routing\Route
	 */
	protected $current;

	/**
	 * The request currently being dispatched.
	 *
	 * @var \Illuminate\Http\Request
	 */
	protected $currentRequest;

	/**
	 * The controller dispatcher instance.
	 *
	 * @var \Illuminate\Routing\ControllerDispatcher
	 */
	protected $controllerDispatcher;

	/**
	 * The controller inspector instance.
	 *
	 * @var \Illuminate\Routing\ControllerInspector
	 */
	protected $inspector;

	/**
	 * Indicates if the router is running filters.
	 *
	 * @var bool
	 */
	protected $filtering = true;

	/**
	 * The registered pattern based filters.
	 *
	 * @var array
	 */
	protected $patternFilters = array();

	/**
	 * The registered regular expression based filters.
	 *
	 * @var array
	 */
	protected $regexFilters = array();

	/**
	 * The registered route value binders.
	 *
	 * @var array
	 */
	protected $binders = array();

	/**
	 * The globally available parameter patterns.
	 *
	 * @var array
	 */
	protected $patterns = array();

	/**
	 * The route group attribute stack.
	 *
	 * @var array
	 */
	protected $groupStack = array();

	/**
	 * All of the verbs supported by the router.
	 *
	 * @var array
	 */
	public static $verbs = array('GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS');

	/**
	 * The default actions for a resourceful controller.
	 *
	 * @var array
	 */
	protected $resourceDefaults = array('index', 'create', 'store', 'show', 'edit', 'update', 'destroy');

	/**
	 * Create a new Router instance.
	 *
	 * @param  \Illuminate\Events\Dispatcher  $events
	 * @param  \Illuminate\Container\Container  $container
	 * @return void
	 */
	public function __construct(Dispatcher $events, Container $container = null)
	{
		$this->events = $events;
		$this->routes = new RouteCollection;
		$this->container = $container ?: new Container;

		$this->bind('_missing', function($v) { return explode('/', $v); });
	}

	/**
	 * Register a new GET route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function get($uri, $action)
	{
		return $this->addRoute(array('GET', 'HEAD'), $uri, $action);
	}

	/**
	 * Register a new POST route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function post($uri, $action)
	{
		return $this->addRoute('POST', $uri, $action);
	}

	/**
	 * Register a new PUT route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function put($uri, $action)
	{
		return $this->addRoute('PUT', $uri, $action);
	}

	/**
	 * Register a new PATCH route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function patch($uri, $action)
	{
		return $this->addRoute('PATCH', $uri, $action);
	}

	/**
	 * Register a new DELETE route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function delete($uri, $action)
	{
		return $this->addRoute('DELETE', $uri, $action);
	}

	/**
	 * Register a new OPTIONS route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function options($uri, $action)
	{
		return $this->addRoute('OPTIONS', $uri, $action);
	}

	/**
	 * Register a new route responding to all verbs.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function any($uri, $action)
	{
		$verbs = array('GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE');

		return $this->addRoute($verbs, $uri, $action);
	}

	/**
	 * Register a new route with the given verbs.
	 *
	 * @param  array|string  $methods
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function match($methods, $uri, $action)
	{
		return $this->addRoute($methods, $uri, $action);
	}

	/**
	 * Register an array of controllers with wildcard routing.
	 *
	 * @param  array  $controllers
	 * @return void
	 */
	public function controllers(array $controllers)
	{
		foreach ($controllers as $uri => $name)
		{
			$this->controller($uri, $name);
		}
	}

	/**
	 * Route a controller to a URI with wildcard routing.
	 *
	 * @param  string  $uri
	 * @param  string  $controller
	 * @param  array   $names
	 * @return void
	 */
	public function controller($uri, $controller, $names = array())
	{
		$prepended = $controller;

		// First, we will check to see if a controller prefix has been registered in
		// the route group. If it has, we will need to prefix it before trying to
		// reflect into the class instance and pull out the method for routing.
		if (count($this->groupStack) > 0)
		{
			$prepended = $this->prependGroupUses($controller);
		}

		$routable = $this->getInspector()->getRoutable($prepended, $uri);

		// When a controller is routed using this method, we use Reflection to parse
		// out all of the routable methods for the controller, then register each
		// route explicitly for the developers, so reverse routing is possible.
		foreach ($routable as $method => $routes)
		{
			foreach ($routes as $route)
			{
				$this->registerInspected($route, $controller, $method, $names);
			}
		}

		$this->addFallthroughRoute($controller, $uri);
	}

	/**
	 * Register an inspected controller route.
	 *
	 * @param  array   $route
	 * @param  string  $controller
	 * @param  string  $method
	 * @param  array   $names
	 * @return void
	 */
	protected function registerInspected($route, $controller, $method, &$names)
	{
		$action = array('uses' => $controller.'@'.$method);

		// If a given controller method has been named, we will assign the name to the
		// controller action array, which provides for a short-cut to method naming
		// so you don't have to define an individual route for these controllers.
		$action['as'] = array_pull($names, $method);

		$this->{$route['verb']}($route['uri'], $action);
	}

	/**
	 * Add a fallthrough route for a controller.
	 *
	 * @param  string  $controller
	 * @param  string  $uri
	 * @return void
	 */
	protected function addFallthroughRoute($controller, $uri)
	{
		$missing = $this->any($uri.'/{_missing}', $controller.'@missingMethod');

		$missing->where('_missing', '(.*)');
	}

	/**
	 * Route a resource to a controller.
	 *
	 * @param  string  $name
	 * @param  string  $controller
	 * @param  array   $options
	 * @return void
	 */
	public function resource($name, $controller, array $options = array())
	{
		// If the resource name contains a slash, we will assume the developer wishes to
		// register these resource routes with a prefix so we will set that up out of
		// the box so they don't have to mess with it. Otherwise, we will continue.
		if (str_contains($name, '/'))
		{
			$this->prefixedResource($name, $controller, $options);

			return;
		}

		// We need to extract the base resource from the resource name. Nested resources
		// are supported in the framework, but we need to know what name to use for a
		// place-holder on the route wildcards, which should be the base resources.
		$base = $this->getResourceWildcard(last(explode('.', $name)));

		$defaults = $this->resourceDefaults;

		foreach ($this->getResourceMethods($defaults, $options) as $m)
		{
			$this->{'addResource'.ucfirst($m)}($name, $base, $controller, $options);
		}
	}

	/**
	 * Build a set of prefixed resource routes.
	 *
	 * @param  string  $name
	 * @param  string  $controller
	 * @param  array   $options
	 * @return void
	 */
	protected function prefixedResource($name, $controller, array $options)
	{
		list($name, $prefix) = $this->getResourcePrefix($name);

		// We need to extract the base resource from the resource name. Nested resources
		// are supported in the framework, but we need to know what name to use for a
		// place-holder on the route wildcards, which should be the base resources.
		$callback = function($me) use ($name, $controller, $options)
		{
			$me->resource($name, $controller, $options);
		};

		return $this->group(compact('prefix'), $callback);
	}

	/**
	 * Extract the resource and prefix from a resource name.
	 *
	 * @param  string  $name
	 * @return array
	 */
	protected function getResourcePrefix($name)
	{
		$segments = explode('/', $name);

		// To get the prefix, we will take all of the name segments and implode them on
		// a slash. This will generate a proper URI prefix for us. Then we take this
		// last segment, which will be considered the final resources name we use.
		$prefix = implode('/', array_slice($segments, 0, -1));

		return array($segments[count($segments) - 1], $prefix);
	}

	/**
	 * Get the applicable resource methods.
	 *
	 * @param  array  $defaults
	 * @param  array  $options
	 * @return array
	 */
	protected function getResourceMethods($defaults, $options)
	{
		if (isset($options['only']))
		{
			return array_intersect($defaults, $options['only']);
		}
		elseif (isset($options['except']))
		{
			return array_diff($defaults, $options['except']);
		}

		return $defaults;
	}

	/**
	 * Get the base resource URI for a given resource.
	 *
	 * @param  string  $resource
	 * @return string
	 */
	public function getResourceUri($resource)
	{
		if ( ! str_contains($resource, '.')) return $resource;

		// Once we have built the base URI, we'll remove the wildcard holder for this
		// base resource name so that the individual route adders can suffix these
		// paths however they need to, as some do not have any wildcards at all.
		$segments = explode('.', $resource);

		$uri = $this->getNestedResourceUri($segments);

		return str_replace('/{'.$this->getResourceWildcard(last($segments)).'}', '', $uri);
	}

	/**
	 * Get the URI for a nested resource segment array.
	 *
	 * @param  array   $segments
	 * @return string
	 */
	protected function getNestedResourceUri(array $segments)
	{
		$me = $this;

		// We will spin through the segments and create a place-holder for each of the
		// resource segments, as well as the resource itself. Then we should get an
		// entire string for the resource URI that contains all nested resources.
		return implode('/', array_map(function($s) use ($me)
		{
			return $s.'/{'.$me->getResourceWildcard($s).'}';

		}, $segments));
	}

	/**
	 * Get the action array for a resource route.
	 *
	 * @param  string  $resource
	 * @param  string  $controller
	 * @param  string  $method
	 * @param  array   $options
	 * @return array
	 */
	protected function getResourceAction($resource, $controller, $method, $options)
	{
		$name = $this->getResourceName($resource, $method, $options);

		return array('as' => $name, 'uses' => $controller.'@'.$method);
	}

	/**
	 * Get the name for a given resource.
	 *
	 * @param  string  $resource
	 * @param  string  $method
	 * @param  array   $options
	 * @return string
	 */
	protected function getResourceName($resource, $method, $options)
	{
		if (isset($options['names'][$method])) return $options['names'][$method];

		// If a global prefix has been assigned to all names for this resource, we will
		// grab that so we can prepend it onto the name when we create this name for
		// the resource action. Otherwise we'll just use an empty string for here.
		$prefix = isset($options['as']) ? $options['as'].'.' : '';

		if (count($this->groupStack) == 0)
		{
			return $prefix.$resource.'.'.$method;
		}

		return $this->getGroupResourceName($prefix, $resource, $method);
	}

	/**
	 * Get the resource name for a grouped resource.
	 *
	 * @param  string  $prefix
	 * @param  string  $resource
	 * @param  string  $method
	 * @return string
	 */
	protected function getGroupResourceName($prefix, $resource, $method)
	{
		$group = str_replace('/', '.', $this->getLastGroupPrefix());

		return trim("{$prefix}{$group}.{$resource}.{$method}", '.');
	}

	/**
	 * Format a resource wildcard for usage.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function getResourceWildcard($value)
	{
		return str_replace('-', '_', $value);
	}

	/**
	 * Add the index method for a resourceful route.
	 *
	 * @param  string  $name
	 * @param  string  $base
	 * @param  string  $controller
	 * @param  array   $options
	 * @return Route
	 */
	protected function addResourceIndex($name, $base, $controller, $options)
	{
		$action = $this->getResourceAction($name, $controller, 'index', $options);

		return $this->get($this->getResourceUri($name), $action);
	}

	/**
	 * Add the create method for a resourceful route.
	 *
	 * @param  string  $name
	 * @param  string  $base
	 * @param  string  $controller
	 * @param  array   $options
	 * @return Route
	 */
	protected function addResourceCreate($name, $base, $controller, $options)
	{
		$action = $this->getResourceAction($name, $controller, 'create', $options);

		return $this->get($this->getResourceUri($name).'/create', $action);
	}

	/**
	 * Add the store method for a resourceful route.
	 *
	 * @param  string  $name
	 * @param  string  $base
	 * @param  string  $controller
	 * @param  array   $options
	 * @return Route
	 */
	protected function addResourceStore($name, $base, $controller, $options)
	{
		$action = $this->getResourceAction($name, $controller, 'store', $options);

		return $this->post($this->getResourceUri($name), $action);
	}

	/**
	 * Add the show method for a resourceful route.
	 *
	 * @param  string  $name
	 * @param  string  $base
	 * @param  string  $controller
	 * @param  array   $options
	 * @return Route
	 */
	protected function addResourceShow($name, $base, $controller, $options)
	{
		$uri = $this->getResourceUri($name).'/{'.$base.'}';

		return $this->get($uri, $this->getResourceAction($name, $controller, 'show', $options));
	}

	/**
	 * Add the edit method for a resourceful route.
	 *
	 * @param  string  $name
	 * @param  string  $base
	 * @param  string  $controller
	 * @param  array   $options
	 * @return Route
	 */
	protected function addResourceEdit($name, $base, $controller, $options)
	{
		$uri = $this->getResourceUri($name).'/{'.$base.'}/edit';

		return $this->get($uri, $this->getResourceAction($name, $controller, 'edit', $options));
	}

	/**
	 * Add the update method for a resourceful route.
	 *
	 * @param  string  $name
	 * @param  string  $base
	 * @param  string  $controller
	 * @param  array   $options
	 * @return void
	 */
	protected function addResourceUpdate($name, $base, $controller, $options)
	{
		$this->addPutResourceUpdate($name, $base, $controller, $options);

		return $this->addPatchResourceUpdate($name, $base, $controller);
	}

	/**
	 * Add the update method for a resourceful route.
	 *
	 * @param  string  $name
	 * @param  string  $base
	 * @param  string  $controller
	 * @param  array   $options
	 * @return Route
	 */
	protected function addPutResourceUpdate($name, $base, $controller, $options)
	{
		$uri = $this->getResourceUri($name).'/{'.$base.'}';

		return $this->put($uri, $this->getResourceAction($name, $controller, 'update', $options));
	}

	/**
	 * Add the update method for a resourceful route.
	 *
	 * @param  string  $name
	 * @param  string  $base
	 * @param  string  $controller
	 * @return void
	 */
	protected function addPatchResourceUpdate($name, $base, $controller)
	{
		$uri = $this->getResourceUri($name).'/{'.$base.'}';

		$this->patch($uri, $controller.'@update');
	}

	/**
	 * Add the destroy method for a resourceful route.
	 *
	 * @param  string  $name
	 * @param  string  $base
	 * @param  string  $controller
	 * @param  array   $options
	 * @return Route
	 */
	protected function addResourceDestroy($name, $base, $controller, $options)
	{
		$action = $this->getResourceAction($name, $controller, 'destroy', $options);

		return $this->delete($this->getResourceUri($name).'/{'.$base.'}', $action);
	}

	/**
	 * Create a route group with shared attributes.
	 *
	 * @param  array    $attributes
	 * @param  Closure  $callback
	 * @return void
	 */
	public function group(array $attributes, Closure $callback)
	{
		$this->updateGroupStack($attributes);

		// Once we have updated the group stack, we will execute the user Closure and
		// merge in the groups attributes when the route is created. After we have
		// run the callback, we will pop the attributes off of this group stack.
		call_user_func($callback, $this);

		array_pop($this->groupStack);
	}

	/**
	 * Update the group stack with the given attributes.
	 *
	 * @param  array  $attributes
	 * @return void
	 */
	protected function updateGroupStack(array $attributes)
	{
		if (count($this->groupStack) > 0)
		{
			$attributes = $this->mergeGroup($attributes, last($this->groupStack));
		}

		$this->groupStack[] = $attributes;
	}

	/**
	 * Merge the given array with the last group stack.
	 *
	 * @param  array  $new
	 * @return array
	 */
	public function mergeWithLastGroup($new)
	{
		return $this->mergeGroup($new, last($this->groupStack));
	}

	/**
	 * Merge the given group attributes.
	 *
	 * @param  array  $new
	 * @param  array  $old
	 * @return array
	 */
	public static function mergeGroup($new, $old)
	{
		$new['namespace'] = static::formatUsesPrefix($new, $old);

		$new['prefix'] = static::formatGroupPrefix($new, $old);

		if (isset($new['domain'])) unset($old['domain']);

		return array_merge_recursive(array_except($old, array('namespace', 'prefix')), $new);
	}

	/**
	 * Format the uses prefix for the new group attributes.
	 *
	 * @param  array  $new
	 * @param  array  $old
	 * @return string
	 */
	protected static function formatUsesPrefix($new, $old)
	{
		if (isset($new['namespace']) && isset($old['namespace']))
		{
			return trim(array_get($old, 'namespace'), '\\').'\\'.trim($new['namespace'], '\\');
		}
		elseif (isset($new['namespace']))
		{
			return trim($new['namespace'], '\\');
		}

		return array_get($old, 'namespace');
	}

	/**
	 * Format the prefix for the new group attributes.
	 *
	 * @param  array  $new
	 * @param  array  $old
	 * @return string
	 */
	protected static function formatGroupPrefix($new, $old)
	{
		if (isset($new['prefix']))
		{
			return trim(array_get($old, 'prefix'), '/').'/'.trim($new['prefix'], '/');
		}

		return array_get($old, 'prefix');
	}

	/**
	 * Get the prefix from the last group on the stack.
	 *
	 * @return string
	 */
	protected function getLastGroupPrefix()
	{
		if (count($this->groupStack) > 0)
		{
			return array_get(last($this->groupStack), 'prefix', '');
		}

		return '';
	}

	/**
	 * Add a route to the underlying route collection.
	 *
	 * @param  array|string  $methods
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	protected function addRoute($methods, $uri, $action)
	{
		return $this->routes->add($this->createRoute($methods, $uri, $action));
	}

	/**
	 * Create a new route instance.
	 *
	 * @param  array|string  $methods
	 * @param  string  $uri
	 * @param  mixed   $action
	 * @return \Illuminate\Routing\Route
	 */
	protected function createRoute($methods, $uri, $action)
	{
		// If the route is routing to a controller we will parse the route action into
		// an acceptable array format before registering it and creating this route
		// instance itself. We need to build the Closure that will call this out.
		if ($this->routingToController($action))
		{
			$action = $this->getControllerAction($action);
		}

		$route = $this->newRoute(
			$methods, $uri = $this->prefix($uri), $action
		);

		$route->where($this->patterns);

		// If we have groups that need to be merged, we will merge them now after this
		// route has already been created and is ready to go. After we're done with
		// the merge we will be ready to return the route back out to the caller.
		if (count($this->groupStack) > 0)
		{
			$this->mergeController($route);
		}

		return $route;
	}

	/**
	 * Create a new Route object.
	 *
	 * @param  array|string $methods
	 * @param  string  $uri
	 * @param  mixed  $action
	 * @return \Illuminate\Routing\Route
	 */
	protected function newRoute($methods, $uri, $action)
	{
		return new Route($methods, $uri, $action);
	}

	/**
	 * Prefix the given URI with the last prefix.
	 *
	 * @param  string  $uri
	 * @return string
	 */
	protected function prefix($uri)
	{
		return trim(trim($this->getLastGroupPrefix(), '/').'/'.trim($uri, '/'), '/') ?: '/';
	}

	/**
	 * Merge the group stack with the controller action.
	 *
	 * @param  \Illuminate\Routing\Route  $route
	 * @return void
	 */
	protected function mergeController($route)
	{
		$action = $this->mergeWithLastGroup($route->getAction());

		$route->setAction($action);
	}

	/**
	 * Determine if the action is routing to a controller.
	 *
	 * @param  array  $action
	 * @return bool
	 */
	protected function routingToController($action)
	{
		if ($action instanceof Closure) return false;

		return is_string($action) || is_string(array_get($action, 'uses'));
	}

	/**
	 * Add a controller based route action to the action array.
	 *
	 * @param  array|string  $action
	 * @return array
	 */
	protected function getControllerAction($action)
	{
		if (is_string($action)) $action = array('uses' => $action);

		// Here we'll get an instance of this controller dispatcher and hand it off to
		// the Closure so it will be used to resolve the class instances out of our
		// IoC container instance and call the appropriate methods on the class.
		if (count($this->groupStack) > 0)
		{
			$action['uses'] = $this->prependGroupUses($action['uses']);
		}

		// Here we'll get an instance of this controller dispatcher and hand it off to
		// the Closure so it will be used to resolve the class instances out of our
		// IoC container instance and call the appropriate methods on the class.
		$action['controller'] = $action['uses'];

		$closure = $this->getClassClosure($action['uses']);

		return array_set($action, 'uses', $closure);
	}

	/**
	 * Get the Closure for a controller based action.
	 *
	 * @param  string  $controller
	 * @return \Closure
	 */
	protected function getClassClosure($controller)
	{
		$me = $this;

		// Here we'll get an instance of this controller dispatcher and hand it off to
		// the Closure so it will be used to resolve the class instances out of our
		// IoC container instance and call the appropriate methods on the class.
		$d = $this->getControllerDispatcher();

		return function() use ($me, $d, $controller)
		{
			$route = $me->current();

			$request = $me->getCurrentRequest();

			// Now we can split the controller and method out of the action string so that we
			// can call them appropriately on the class. This controller and method are in
			// in the Class@method format and we need to explode them out then use them.
			list($class, $method) = explode('@', $controller);

			return $d->dispatch($route, $request, $class, $method);
		};
	}

	/**
	 * Prepend the last group uses onto the use clause.
	 *
	 * @param  string  $uses
	 * @return string
	 */
	protected function prependGroupUses($uses)
	{
		$group = last($this->groupStack);

		return isset($group['namespace']) ? $group['namespace'].'\\'.$uses : $uses;
	}

	/**
	 * Dispatch the request to the application.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function dispatch(Request $request)
	{
		$this->currentRequest = $request;

		// If no response was returned from the before filter, we will call the proper
		// route instance to get the response. If no route is found a response will
		// still get returned based on why no routes were found for this request.
		$response = $this->callFilter('before', $request);

		if (is_null($response))
		{
			$response = $this->dispatchToRoute($request);
		}

		$response = $this->prepareResponse($request, $response);

		// Once this route has run and the response has been prepared, we will run the
		// after filter to do any last work on the response or for this application
		// before we will return the response back to the consuming code for use.
		$this->callFilter('after', $request, $response);

		return $response;
	}

	/**
	 * Dispatch the request to a route and return the response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return mixed
	 */
	public function dispatchToRoute(Request $request)
	{
		$route = $this->findRoute($request);

		$this->events->fire('router.matched', array($route, $request));

		// Once we have successfully matched the incoming request to a given route we
		// can call the before filters on that route. This works similar to global
		// filters in that if a response is returned we will not call the route.
		$response = $this->callRouteBefore($route, $request);

		if (is_null($response))
		{
			$response = $route->run($request);
		}

		$response = $this->prepareResponse($request, $response);

		// After we have a prepared response from the route or filter we will call to
		// the "after" filters to do any last minute processing on this request or
		// response object before the response is returned back to the consumer.
		$this->callRouteAfter($route, $request, $response);

		return $response;
	}

	/**
	 * Find the route matching a given request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Routing\Route
	 */
	protected function findRoute($request)
	{
		$this->current = $route = $this->routes->match($request);

		return $this->substituteBindings($route);
	}

	/**
	 * Substitute the route bindings onto the route.
	 *
	 * @param  \Illuminate\Routing\Route  $route
	 * @return \Illuminate\Routing\Route
	 */
	protected function substituteBindings($route)
	{
		foreach ($route->parameters() as $key => $value)
		{
			if (isset($this->binders[$key]))
			{
				$route->setParameter($key, $this->performBinding($key, $value, $route));
			}
		}

		return $route;
	}

	/**
	 * Call the binding callback for the given key.
	 *
	 * @param  string  $key
	 * @param  string  $value
	 * @param  \Illuminate\Routing\Route  $route
	 * @return mixed
	 */
	protected function performBinding($key, $value, $route)
	{
		return call_user_func($this->binders[$key], $value, $route);
	}

	/**
	 * Register a route matched event listener.
	 *
	 * @param  callable  $callback
	 * @return void
	 */
	public function matched($callback)
	{
		$this->events->listen('router.matched', $callback);
	}

	/**
	 * Register a new "before" filter with the router.
	 *
	 * @param  mixed  $callback
	 * @return void
	 */
	public function before($callback)
	{
		$this->addGlobalFilter('before', $callback);
	}

	/**
	 * Register a new "after" filter with the router.
	 *
	 * @param  mixed  $callback
	 * @return void
	 */
	public function after($callback)
	{
		$this->addGlobalFilter('after', $callback);
	}

	/**
	 * Register a new global filter with the router.
	 *
	 * @param  string  $filter
	 * @param  mixed   $callback
	 * @return void
	 */
	protected function addGlobalFilter($filter, $callback)
	{
		$this->events->listen('router.'.$filter, $this->parseFilter($callback));
	}

	/**
	 * Register a new filter with the router.
	 *
	 * @param  string  $name
	 * @param  mixed  $callback
	 * @return void
	 */
	public function filter($name, $callback)
	{
		$this->events->listen('router.filter: '.$name, $this->parseFilter($callback));
	}

	/**
	 * Parse the registered filter.
	 *
	 * @param  \Closure|string  $callback
	 * @return mixed
	 */
	protected function parseFilter($callback)
	{
		if (is_string($callback) && ! str_contains($callback, '@'))
		{
			return $callback.'@filter';
		}
		else
		{
			return $callback;
		}
	}

	/**
	 * Register a pattern-based filter with the router.
	 *
	 * @param  string  $pattern
	 * @param  string  $name
	 * @param  array|null  $methods
	 */
	public function when($pattern, $name, $methods = null)
	{
		if ( ! is_null($methods)) $methods = array_map('strtoupper', (array) $methods);

		$this->patternFilters[$pattern][] = compact('name', 'methods');
	}

	/**
	 * Register a regular expression based filter with the router.
	 *
	 * @param  string     $pattern
	 * @param  string     $name
	 * @param  array|null $methods
	 * @return void
	 */
	public function whenRegex($pattern, $name, $methods = null)
	{
		if ( ! is_null($methods)) $methods = array_map('strtoupper', (array) $methods);

		$this->regexFilters[$pattern][] = compact('name', 'methods');
	}

	/**
	 * Register a model binder for a wildcard.
	 *
	 * @param  string  $key
	 * @param  string  $class
	 * @param  \Closure  $callback
	 * @return void
	 *
	 * @throws NotFoundHttpException
	 */
	public function model($key, $class, Closure $callback = null)
	{
		return $this->bind($key, function($value) use ($class, $callback)
		{
			if (is_null($value)) return null;

			// For model binders, we will attempt to retrieve the models using the find
			// method on the model instance. If we cannot retrieve the models we'll
			// throw a not found exception otherwise we will return the instance.
			if ($model = with(new $class)->find($value))
			{
				return $model;
			}

			// If a callback was supplied to the method we will call that to determine
			// what we should do when the model is not found. This just gives these
			// developer a little greater flexibility to decide what will happen.
			if ($callback instanceof Closure)
			{
				return call_user_func($callback);
			}

			throw new NotFoundHttpException;
		});
	}

	/**
	 * Add a new route parameter binder.
	 *
	 * @param  string  $key
	 * @param  callable  $binder
	 * @return void
	 */
	public function bind($key, $binder)
	{
		$this->binders[str_replace('-', '_', $key)] = $binder;
	}

	/**
	 * Set a global where pattern on all routes
	 *
	 * @param  string  $key
	 * @param  string  $pattern
	 * @return void
	 */
	public function pattern($key, $pattern)
	{
		$this->patterns[$key] = $pattern;
	}

	/**
	 * Call the given filter with the request and response.
	 *
	 * @param  string  $filter
	 * @param  \Illuminate\Http\Request   $request
	 * @param  \Illuminate\Http\Response  $response
	 * @return mixed
	 */
	protected function callFilter($filter, $request, $response = null)
	{
		if ( ! $this->filtering) return null;

		return $this->events->until('router.'.$filter, array($request, $response));
	}

	/**
	 * Call the given route's before filters.
	 *
	 * @param  \Illuminate\Routing\Route  $route
	 * @param  \Illuminate\Http\Request  $request
	 * @return mixed
	 */
	public function callRouteBefore($route, $request)
	{
		$response = $this->callPatternFilters($route, $request);

		return $response ?: $this->callAttachedBefores($route, $request);
	}

	/**
	 * Call the pattern based filters for the request.
	 *
	 * @param  \Illuminate\Routing\Route  $route
	 * @param  \Illuminate\Http\Request  $request
	 * @return mixed|null
	 */
	protected function callPatternFilters($route, $request)
	{
		foreach ($this->findPatternFilters($request) as $filter => $parameters)
		{
			$response = $this->callRouteFilter($filter, $parameters, $route, $request);

			if ( ! is_null($response)) return $response;
		}
	}

	/**
	 * Find the patterned filters matching a request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	public function findPatternFilters($request)
	{
		$results = array();

		list($path, $method) = array($request->path(), $request->getMethod());

		foreach ($this->patternFilters as $pattern => $filters)
		{
			// To find the patterned middlewares for a request, we just need to check these
			// registered patterns against the path info for the current request to this
			// applications, and when it matches we will merge into these middlewares.
			if (str_is($pattern, $path))
			{
				$merge = $this->patternsByMethod($method, $filters);

				$results = array_merge($results, $merge);
			}
		}

		foreach ($this->regexFilters as $pattern => $filters)
		{
			// To find the patterned middlewares for a request, we just need to check these
			// registered patterns against the path info for the current request to this
			// applications, and when it matches we will merge into these middlewares.
			if (preg_match($pattern, $path))
			{
				$merge = $this->patternsByMethod($method, $filters);

				$results = array_merge($results, $merge);
			}
		}

		return $results;
	}

	/**
	 * Filter pattern filters that don't apply to the request verb.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  array  $filters
	 * @return array
	 */
	protected function patternsByMethod($method, $filters)
	{
		$results = array();

		foreach ($filters as $filter)
		{
			// The idea here is to check and see if the pattern filter applies to this HTTP
			// request based on the request methods. Pattern filters might be limited by
			// the request verb to make it simply to assign to the given verb at once.
			if ($this->filterSupportsMethod($filter, $method))
			{
				$parsed = Route::parseFilters($filter['name']);

				$results = array_merge($results, $parsed);
			}
		}

		return $results;
	}

	/**
	 * Determine if the given pattern filters applies to a given method.
	 *
	 * @param  array  $filter
	 * @param  array  $method
	 * @return bool
	 */
	protected function filterSupportsMethod($filter, $method)
	{
		$methods = $filter['methods'];

		return (is_null($methods) || in_array($method, $methods));
	}

	/**
	 * Call the given route's before (non-pattern) filters.
	 *
	 * @param  \Illuminate\Routing\Route  $route
	 * @param  \Illuminate\Http\Request  $request
	 * @return mixed
	 */
	protected function callAttachedBefores($route, $request)
	{
		foreach ($route->beforeFilters() as $filter => $parameters)
		{
			$response = $this->callRouteFilter($filter, $parameters, $route, $request);

			if ( ! is_null($response)) return $response;
		}
	}

	/**
	 * Call the given route's before filters.
	 *
	 * @param  \Illuminate\Routing\Route  $route
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Illuminate\Http\Response  $response
	 * @return mixed
	 */
	public function callRouteAfter($route, $request, $response)
	{
		foreach ($route->afterFilters() as $filter => $parameters)
		{
			$this->callRouteFilter($filter, $parameters, $route, $request, $response);
		}
	}

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
	public function callRouteFilter($filter, $parameters, $route, $request, $response = null)
	{
		if ( ! $this->filtering) return null;

		$data = array_merge(array($route, $request, $response), $parameters);

		return $this->events->until('router.filter: '.$filter, $this->cleanFilterParameters($data));
	}

	/**
	 * Clean the parameters being passed to a filter callback.
	 *
	 * @param  array  $parameters
	 * @return array
	 */
	protected function cleanFilterParameters(array $parameters)
	{
		return array_filter($parameters, function($p)
		{
			return ! is_null($p) && $p !== '';
		});
	}

	/**
	 * Create a response instance from the given value.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Request  $request
	 * @param  mixed  $response
	 * @return \Illuminate\Http\Response
	 */
	protected function prepareResponse($request, $response)
	{
		if ( ! $response instanceof SymfonyResponse)
		{
			$response = new Response($response);
		}

		return $response->prepare($request);
	}

	/**
	 * Run a callback with filters disable on the router.
	 *
	 * @param  callable  $callback
	 * @return void
	 */
	public function withoutFilters($callback)
	{
		$this->disableFilters();

		call_user_func($callback);

		$this->enableFilters();
	}

	/**
	 * Enable route filtering on the router.
	 *
	 * @return void
	 */
	public function enableFilters()
	{
		$this->filtering = true;
	}

	/**
	 * Disable route filtering on the router.
	 *
	 * @return void
	 */
	public function disableFilters()
	{
		$this->filtering = false;
	}

	/**
	 * Get a route parameter for the current route.
	 *
	 * @param  string  $key
	 * @param  string  $default
	 * @return mixed
	 */
	public function input($key, $default = null)
	{
		return $this->current()->parameter($key, $default);
	}

	/**
	 * Get the currently dispatched route instance.
	 *
	 * @return \Illuminate\Routing\Route
	 */
	public function getCurrentRoute()
	{
		return $this->current();
	}

	/**
	 * Get the currently dispatched route instance.
	 *
	 * @return \Illuminate\Routing\Route
	 */
	public function current()
	{
		return $this->current;
	}

	/**
	 * Get the current route name.
	 *
	 * @return string|null
	 */
	public function currentRouteName()
	{
		return ($this->current()) ? $this->current()->getName() : null;
	}

	/**
	 * Alias for the "currentRouteNamed" method.
	 *
	 * @param  dynamic  string
	 * @return bool
	 */
	public function is()
	{
		foreach (func_get_args() as $pattern)
		{
			if (str_is($pattern, $this->currentRouteName()))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Determine if the current route matches a given name.
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public function currentRouteNamed($name)
	{
		return ($this->current()) ? $this->current()->getName() == $name : false;
	}

	/**
	 * Get the current route action.
	 *
	 * @return string|null
	 */
	public function currentRouteAction()
	{
		$action = $this->current()->getAction();

		return isset($action['controller']) ? $action['controller'] : null;
	}

	/**
	 * Alias for the "currentRouteUses" method.
	 *
	 * @param  dynamic  string
	 * @return bool
	 */
	public function isAction()
	{
		foreach (func_get_args() as $pattern)
		{
			if (str_is($pattern, $this->currentRouteAction()))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Determine if the current route action matches a given action.
	 *
	 * @param  string  $action
	 * @return bool
	 */
	public function currentRouteUses($action)
	{
		return $this->currentRouteAction() == $action;
	}

	/**
	 * Get the request currently being dispatched.
	 *
	 * @return \Illuminate\Http\Request
	 */
	public function getCurrentRequest()
	{
		return $this->currentRequest;
	}

	/**
	 * Get the underlying route collection.
	 *
	 * @return \Illuminate\Routing\RouteCollection
	 */
	public function getRoutes()
	{
		return $this->routes;
	}

	/**
	 * Get the controller dispatcher instance.
	 *
	 * @return \Illuminate\Routing\ControllerDispatcher
	 */
	public function getControllerDispatcher()
	{
		if (is_null($this->controllerDispatcher))
		{
			$this->controllerDispatcher = new ControllerDispatcher($this, $this->container);
		}

		return $this->controllerDispatcher;
	}

	/**
	 * Set the controller dispatcher instance.
	 *
	 * @param  \Illuminate\Routing\ControllerDispatcher  $dispatcher
	 * @return void
	 */
	public function setControllerDispatcher(ControllerDispatcher $dispatcher)
	{
		$this->controllerDispatcher = $dispatcher;
	}

	/**
	 * Get a controller inspector instance.
	 *
	 * @return \Illuminate\Routing\ControllerInspector
	 */
	public function getInspector()
	{
		return $this->inspector ?: $this->inspector = new ControllerInspector;
	}

	/**
	 * Get the response for a given request.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Request  $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function handle(SymfonyRequest $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
	{
		return $this->dispatch(Request::createFromBase($request));
	}

}
