<?php namespace Illuminate\Routing;

use Illuminate\Http\Request;
use Illuminate\Routing\Matching\UriValidator;
use Illuminate\Routing\Matching\HostValidator;
use Illuminate\Routing\Matching\MethodValidator;
use Illuminate\Routing\Matching\SchemeValidator;
use Symfony\Component\Routing\Route as SymfonyRoute;

class Route {

	/**
	 * The URI pattern the route responds to.
	 *
	 * @var string
	 */
	protected $uri;

	/**
	 * The HTTP methods the route responds to.
	 *
	 * @var array
	 */
	protected $methods;

	/**
	 * The route action array.
	 *
	 * @var array
	 */
	protected $action;

	/**
	 * The default values for the route.
	 *
	 * @var array
	 */
	protected $defaults = array();

	/**
	 * The regular expression requirements.
	 *
	 * @var array
	 */
	protected $wheres = array();

	/**
	 * The array of matched parameters.
	 *
	 * @var array
	 */
	protected $parameters;

	/**
	 * The parameter names for the route.
	 *
	 * @var array|null
	 */
	protected $parameterNames;

	/**
	 * The compiled version of the route.
	 *
	 * @var \Symfony\Component\Routing\CompiledRoute
	 */
	protected $compiled;

	/**
	 * The validators used by the routes.
	 *
	 * @var array
	 */
	protected static $validators;

	/**
	 * Create a new Route instance.
	 *
	 * @param  array   $methods
	 * @param  string  $uri
	 * @param  \Closure|array  $action
	 * @return void
	 */
	public function __construct($methods, $uri, $action)
	{
		$this->uri = $uri;
		$this->methods = (array) $methods;
		$this->action = $this->parseAction($action);

		if (isset($this->action['prefix']))
		{
			$this->prefix($this->action['prefix']);
		}
	}

	/**
	 * Run the route action and return the response.
	 *
	 * @return mixed
	 */
	public function run()
	{
		$parameters = array_filter($this->parameters(), function($p) { return isset($p); });

		return call_user_func_array($this->action['uses'], $parameters);
	}

	/**
	 * Determine if the route matches given request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  bool  $includingMethod
	 * @return bool
	 */
	public function matches(Request $request, $includingMethod = true)
	{
		$this->compileRoute();

		foreach ($this->getValidators() as $validator)
		{
			if ( ! $includingMethod && $validator instanceof MethodValidator) continue;

			if ( ! $validator->matches($this, $request)) return false;
		}

		return true;
	}

	/**
	 * Compile the route into a Symfony CompiledRoute instance.
	 *
	 * @return void
	 */
	protected function compileRoute()
	{
		$optionals = $this->extractOptionalParameters();

		$uri = preg_replace('/\{(\w+?)\?\}/', '{$1}', $this->uri);

		$this->compiled = with(

			new SymfonyRoute($uri, $optionals, $this->wheres, array(), $this->domain() ?: '')

		)->compile();
	}

	/**
	 * Get the optional parameters for the route.
	 *
	 * @return array
	 */
	protected function extractOptionalParameters()
	{
		preg_match_all('/\{(\w+?)\?\}/', $this->uri, $matches);

		$optional = array();

		if (isset($matches[1]))
		{
			foreach ($matches[1] as $key) { $optional[$key] = null; }
		}

		return $optional;
	}

	/**
	 * Get the "before" filters for the route.
	 *
	 * @return array
	 */
	public function beforeFilters()
	{
		if ( ! isset($this->action['before'])) return array();

		return $this->parseFilters($this->action['before']);
	}

	/**
	 * Get the "after" filters for the route.
	 *
	 * @return array
	 */
	public function afterFilters()
	{
		if ( ! isset($this->action['after'])) return array();

		return $this->parseFilters($this->action['after']);
	}

	/**
	 * Parse the given filter string.
	 *
	 * @param  string  $filters
	 * @return array
	 */
	public static function parseFilters($filters)
	{
		return array_build(static::explodeFilters($filters), function($key, $value)
		{
			return Route::parseFilter($value);
		});
	}

	/**
	 * Turn the filters into an array if they aren't already.
	 *
	 * @param  array|string  $filters
	 * @return array
	 */
	protected static function explodeFilters($filters)
	{
		if (is_array($filters)) return static::explodeArrayFilters($filters);

		return explode('|', $filters);
	}

	/**
	 * Flatten out an array of filter declarations.
	 *
	 * @param  array  $filters
	 * @return array
	 */
	protected static function explodeArrayFilters(array $filters)
	{
		$results = array();

		foreach ($filters as $filter)
		{
			$results = array_merge($results, explode('|', $filter));
		}

		return $results;
	}

	/**
	 * Parse the given filter into name and parameters.
	 *
	 * @param  string  $filter
	 * @return array
	 */
	public static function parseFilter($filter)
	{
		if ( ! str_contains($filter, ':')) return array($filter, array());

		return static::parseParameterFilter($filter);
	}

	/**
	 * Parse a filter with parameters.
	 *
	 * @param  string  $filter
	 * @return array
	 */
	protected static function parseParameterFilter($filter)
	{
		list($name, $parameters) = explode(':', $filter, 2);

		return array($name, explode(',', $parameters));
	}

	/**
	 * Get a given parameter from the route.
	 *
	 * @param  string  $name
	 * @param  mixed  $default
	 * @return string
	 */
	public function getParameter($name, $default = null)
	{
		return $this->parameter($name, $default);
	}

	/**
	 * Get a given parameter from the route.
	 *
	 * @param  string  $name
	 * @param  mixed  $default
	 * @return string
	 */
	public function parameter($name, $default = null)
	{
		return array_get($this->parameters(), $name) ?: $default;
	}

	/**
	 * Set a parameter to the given value.
	 *
	 * @param  string  $name
	 * @param  mixed  $value
	 * @return void
	 */
	public function setParameter($name, $value)
	{
		$this->parameters();

		$this->parameters[$name] = $value;
	}

	/**
	 * Unset a parameter on the route if it is set.
	 *
	 * @param  string $name
	 * @return void
	 */
	public function forgetParameter($name)
	{
		$this->parameters();

		unset($this->parameters[$name]);
	}

	/**
	 * Get the key / value list of parameters for the route.
	 *
	 * @return array
	 *
	 * @throws \LogicException
	 */
	public function parameters()
	{
		if (isset($this->parameters))
		{
			return array_map(function($value)
			{
				return is_string($value) ? urldecode($value) : $value;

			}, $this->parameters);
		}

		throw new \LogicException("Route is not bound.");
	}

	/**
	 * Get the key / value list of parameters without null values.
	 *
	 * @return array
	 */
	public function parametersWithoutNulls()
	{
		return array_filter($this->parameters(), function($p) { return ! is_null($p); });
	}

	/**
	 * Get all of the parameter names for the route.
	 *
	 * @return array
	 */
	public function parameterNames()
	{
		if (isset($this->parameterNames)) return $this->parameterNames;

		return $this->parameterNames = $this->compileParameterNames();
	}

	/**
	 * Get the parameter names for the route.
	 *
	 * @return array
	 */
	protected function compileParameterNames()
	{
		preg_match_all('/\{(.*?)\}/', $this->domain().$this->uri, $matches);

		return array_map(function($m) { return trim($m, '?'); }, $matches[1]);
	}

	/**
	 * Bind the route to a given request for execution.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Routing\Route
	 */
	public function bind(Request $request)
	{
		$this->compileRoute();

		$this->bindParameters($request);

		return $this;
	}

	/**
	 * Extract the parameter list from the request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	public function bindParameters(Request $request)
	{
		// If the route has a regular expression for the host part of the URI, we will
		// compile that and get the parameter matches for this domain. We will then
		// merge them into this parameters array so that this array is completed.
		$params = $this->matchToKeys(

			array_slice($this->bindPathParameters($request), 1)

		);

		// If the route has a regular expression for the host part of the URI, we will
		// compile that and get the parameter matches for this domain. We will then
		// merge them into this parameters array so that this array is completed.
		if ( ! is_null($this->compiled->getHostRegex()))
		{
			$params = $this->bindHostParameters(
				$request, $params
			);
		}

		return $this->parameters = $this->replaceDefaults($params);
	}

	/**
	 * Get the parameter matches for the path portion of the URI.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	protected function bindPathParameters(Request $request)
	{
		preg_match($this->compiled->getRegex(), '/'.$request->decodedPath(), $matches);

		return $matches;
	}

	/**
	 * Extract the parameter list from the host part of the request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	protected function bindHostParameters(Request $request, $parameters)
	{
		preg_match($this->compiled->getHostRegex(), $request->getHost(), $matches);

		return array_merge($this->matchToKeys(array_slice($matches, 1)), $parameters);
	}

	/**
	 * Combine a set of parameter matches with the route's keys.
	 *
	 * @param  array  $matches
	 * @return array
	 */
	protected function matchToKeys(array $matches)
	{
		if (count($this->parameterNames()) == 0) return array();

		$parameters = array_intersect_key($matches, array_flip($this->parameterNames()));

		return array_filter($parameters, function($value)
		{
			return is_string($value) && strlen($value) > 0;
		});
	}

	/**
	 * Replace null parameters with their defaults.
	 *
	 * @param  array  $parameters
	 * @return array
	 */
	protected function replaceDefaults(array $parameters)
	{
		foreach ($parameters as $key => &$value)
		{
			$value = isset($value) ? $value : array_get($this->defaults, $key);
		}

		return $parameters;
	}

	/**
	 * Parse the route action into a standard array.
	 *
	 * @param  \Closure|array  $action
	 * @return array
	 */
	protected function parseAction($action)
	{
		// If the action is already a Closure instance, we will just set that instance
		// as the "uses" property, because there is nothing else we need to do when
		// it is available. Otherwise we will need to find it in the action list.
		if (is_callable($action))
		{
			return array('uses' => $action);
		}

		// If no "uses" property has been set, we will dig through the array to find a
		// Closure instance within this list. We will set the first Closure we come
		// across into the "uses" property that will get fired off by this route.
		elseif ( ! isset($action['uses']))
		{
			$action['uses'] = $this->findClosure($action);
		}

		return $action;
	}

	/**
	 * Find the Closure in an action array.
	 *
	 * @param  array  $action
	 * @return \Closure
	 */
	protected function findClosure(array $action)
	{
		return array_first($action, function($key, $value)
		{
			return is_callable($value);
		});
	}

	/**
	 * Get the route validators for the instance.
	 *
	 * @return array
	 */
	public static function getValidators()
	{
		if (isset(static::$validators)) return static::$validators;

		// To match the route, we will use a chain of responsibility pattern with the
		// validator implementations. We will spin through each one making sure it
		// passes and then we will know if the route as a whole matches request.
		return static::$validators = array(
			new MethodValidator, new SchemeValidator,
			new HostValidator, new UriValidator,
		);
	}

	/**
	 * Add before filters to the route.
	 *
	 * @param  string  $filters
	 * @return \Illuminate\Routing\Route
	 */
	public function before($filters)
	{
		return $this->addFilters('before', $filters);
	}

	/**
	 * Add after filters to the route.
	 *
	 * @param  string  $filters
	 * @return \Illuminate\Routing\Route
	 */
	public function after($filters)
	{
		return $this->addFilters('after', $filters);
	}

	/**
	 * Add the given filters to the route by type.
	 *
	 * @param  string  $type
	 * @param  string  $filters
	 * @return \Illuminate\Routing\Route
	 */
	protected function addFilters($type, $filters)
	{
		if (isset($this->action[$type]))
		{
			$this->action[$type] .= '|'.$filters;
		}
		else
		{
			$this->action[$type] = $filters;
		}

		return $this;
	}

	/**
	 * Set a default value for the route.
	 *
	 * @param  string  $key
	 * @param  mixed  $value
	 * @return \Illuminate\Routing\Route
	 */
	public function defaults($key, $value)
	{
		$this->defaults[$key] = $value;

		return $this;
	}

	/**
	 * Set a regular expression requirement on the route.
	 *
	 * @param  array|string  $name
	 * @param  string  $expression
	 * @return \Illuminate\Routing\Route
	 */
	public function where($name, $expression = null)
	{
		foreach ($this->parseWhere($name, $expression) as $name => $expression)
		{
			$this->wheres[$name] = $expression;
		}

		return $this;
	}

	/**
	 * Parse arguments to the where method into an array.
	 *
	 * @param  array|string  $name
	 * @param  string  $expression
	 * @return \Illuminate\Routing\Route
	 */
	protected function parseWhere($name, $expression)
	{
		return is_array($name) ? $name : array($name => $expression);
	}

	/**
	 * Set a list of regular expression requirements on the route.
	 *
	 * @param  array  $wheres
	 * @return \Illuminate\Routing\Route
	 */
	protected function whereArray(array $wheres)
	{
		foreach ($wheres as $name => $expression)
		{
			$this->where($name, $expression);
		}

		return $this;
	}

	/**
	 * Add a prefix to the route URI.
	 *
	 * @param  string  $prefix
	 * @return \Illuminate\Routing\Route
	 */
	public function prefix($prefix)
	{
		$this->uri = trim($prefix, '/').'/'.trim($this->uri, '/');

		return $this;
	}

	/**
	 * Get the URI associated with the route.
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->uri();
	}

	/**
	 * Get the URI associated with the route.
	 *
	 * @return string
	 */
	public function uri()
	{
		return $this->uri;
	}

	/**
	 * Get the HTTP verbs the route responds to.
	 *
	 * @return array
	 */
	public function getMethods()
	{
		return $this->methods();
	}

	/**
	 * Get the HTTP verbs the route responds to.
	 *
	 * @return array
	 */
	public function methods()
	{
		return $this->methods;
	}

	/**
	 * Determine if the route only responds to HTTP requests.
	 *
	 * @return bool
	 */
	public function httpOnly()
	{
		return in_array('http', $this->action, true);
	}

	/**
	 * Determine if the route only responds to HTTPS requests.
	 *
	 * @return bool
	 */
	public function httpsOnly()
	{
		return $this->secure();
	}

	/**
	 * Determine if the route only responds to HTTPS requests.
	 *
	 * @return bool
	 */
	public function secure()
	{
		return in_array('https', $this->action, true);
	}

	/**
	 * Get the domain defined for the route.
	 *
	 * @return string|null
	 */
	public function domain()
	{
		return array_get($this->action, 'domain');
	}

	/**
	 * Get the URI that the route responds to.
	 *
	 * @return string
	 */
	public function getUri()
	{
		return $this->uri;
	}

	/**
	 * Set the URI that the route responds to.
	 *
	 * @param  string  $uri
	 * @return \Illuminate\Routing\Route
	 */
	public function setUri($uri)
	{
		$this->uri = $uri;

		return $this;
	}

	/**
	 * Get the prefix of the route instance.
	 *
	 * @return string
	 */
	public function getPrefix()
	{
		return array_get($this->action, 'prefix');
	}

	/**
	 * Get the name of the route instance.
	 *
	 * @return string
	 */
	public function getName()
	{
		return array_get($this->action, 'as');
	}

	/**
	 * Get the action name for the route.
	 *
	 * @return string
	 */
	public function getActionName()
	{
		return array_get($this->action, 'controller', 'Closure');
	}

	/**
	 * Get the action array for the route.
	 *
	 * @return array
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * Set the action array for the route.
	 *
	 * @param  array  $action
	 * @return \Illuminate\Routing\Route
	 */
	public function setAction(array $action)
	{
		$this->action = $action;

		return $this;
	}

	/**
	 * Get the compiled version of the route.
	 *
	 * @return \Symfony\Component\Routing\CompiledRoute
	 */
	public function getCompiled()
	{
		return $this->compiled;
	}

}
