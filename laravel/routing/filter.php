<?php namespace Laravel\Routing;

use Closure;
use Laravel\Bundle;
use Laravel\Request;

class Filter {

	/**
	 * The route filters for the application.
	 *
	 * @var array
	 */
	public static $filters = array();

	/**
	 * The route filters that are based on a pattern.
	 *
	 * @var array
	 */
	public static $patterns = array();

	/**
	 * All of the registered filter aliases.
	 *
	 * @var array
	 */
	public static $aliases = array();

	/**
	 * Register a filter for the application.
	 *
	 * <code>
	 *		// Register a closure as a filter
	 *		Filter::register('before', function() {});
	 *
	 *		// Register a class callback as a filter
	 *		Filter::register('before', array('Class', 'method'));
	 * </code>
	 *
	 * @param  string  $name
	 * @param  mixed   $callback
	 * @return void
	 */
	public static function register($name, $callback)
	{
		if (isset(static::$aliases[$name])) $name = static::$aliases[$name];

		// If the filter starts with "pattern: ", the filter is being setup to match on
		// all requests that match a given pattern. This is nice for defining filters
		// that handle all URIs beginning with "admin" for example.
		if (starts_with($name, 'pattern: '))
		{
			foreach (explode(', ', substr($name, 9)) as $pattern)
			{
				static::$patterns[$pattern] = $callback;
			}
		}
		else
		{
			static::$filters[$name] = $callback;
		}
	}

	/**
	 * Alias a filter so it can be used by another name.
	 *
	 * This is convenient for shortening filters that are registered by bundles.
	 *
	 * @param  string  $filter
	 * @param  string  $alias
	 * @return void
	 */
	public static function alias($filter, $alias)
	{
		static::$aliases[$alias] = $filter;
	}

	/**
	 * Parse a filter definition into an array of filters.
	 *
	 * @param  string|array  $filters
	 * @return array
	 */
	public static function parse($filters)
	{
		return (is_string($filters)) ? explode('|', $filters) : (array) $filters;
	}

	/**
	 * Call a filter or set of filters.
	 *
	 * @param  array   $collections
	 * @param  array   $pass
	 * @param  bool    $override
	 * @return mixed
	 */
	public static function run($collections, $pass = array(), $override = false)
	{
		foreach ($collections as $collection)
		{
			foreach ($collection->filters as $filter)
			{
				list($filter, $parameters) = $collection->get($filter);

				// We will also go ahead and start the bundle for the developer. This allows
				// the developer to specify bundle filters on routes without starting the
				// bundle manually, and performance is improved by lazy-loading.
				Bundle::start(Bundle::name($filter));

				if ( ! isset(static::$filters[$filter])) continue;

				$callback = static::$filters[$filter];

				// Parameters may be passed into filters by specifying the list of parameters
				// as an array, or by registering a Closure which will return the array of
				// parameters. If parameters are present, we will merge them with the
				// parameters that were given to the method.
				$response = call_user_func_array($callback, array_merge($pass, $parameters));

				// "Before" filters may override the request cycle. For example, an auth
				// filter may redirect a user to a login view if they are not logged in.
				// Because of this, we will return the first filter response if
				// overriding is enabled for the filter collections
				if ( ! is_null($response) and $override)
				{
					return $response;
				}				
			}
		}
	}

}

class Filter_Collection {

	/**
	 * The filters contained by the collection.
	 *
	 * @var string|array
	 */
	public $filters = array();

	/**
	 * The parameters specified for the filter.
	 *
	 * @var mixed
	 */
	public $parameters;

	/**
	 * The included controller methods.
	 *
	 * @var array
	 */
	public $only = array();

	/**
	 * The excluded controller methods.
	 *
	 * @var array
	 */
	public $except = array();

	/**
	 * The HTTP methods for which the filter applies.
	 *
	 * @var array
	 */
	public $methods = array();

	/**
	 * Create a new filter collection instance.
	 *
	 * @param  string|array  $filters
	 * @param  mixed         $parameters
	 * @return void
	 */
	public function __construct($filters, $parameters = null)
	{
		$this->parameters = $parameters;
		$this->filters = Filter::parse($filters);
	}

	/**
	 * Parse the filter string, returning the filter name and parameters.
	 *
	 * @param  string  $filter
	 * @return array
	 */
	public function get($filter)
	{
		// If the parameters were specified by passing an array into the collection,
		// then we will simply return those parameters. Combining passed parameters
		// with parameters specified directly in the filter attachment is not
		// currently supported by the framework.
		if ( ! is_null($this->parameters))
		{
			return array($filter, $this->parameters());
		}

		// If no parameters were specified when the collection was created, we will
		// check the filter string itself to see if the parameters were injected
		// into the string as raw values, such as "role:admin".
		if (($colon = strpos(Bundle::element($filter), ':')) !== false)
		{
			$parameters = explode(',', substr(Bundle::element($filter), $colon + 1));

			// If the filter belongs to a bundle, we need to re-calculate the position
			// of the parameter colon, since we originally calculated it without the
			// bundle identifier because the identifier uses colons as well.
			if (($bundle = Bundle::name($filter)) !== DEFAULT_BUNDLE)
			{
				$colon = strlen($bundle.'::') + $colon;
			}

			return array(substr($filter, 0, $colon), $parameters);
		}

		// If no parameters were specified when the collection was created or
		// in the filter string, we will just return the filter name as is
		// and give back an empty array of parameters.
		return array($filter, array());
	}

	/**
	 * Evaluate the collection's parameters and return a parameters array.
	 *
	 * @return array
	 */
	protected function parameters()
	{
		if ($this->parameters instanceof Closure)
		{
			$this->parameters = call_user_func($this->parameters);
		}

		return $this->parameters;
	}

	/**
	 * Determine if this collection's filters apply to a given method.
	 *
	 * @param  string  $method
	 * @return bool
	 */
	public function applies($method)
	{
		if (count($this->only) > 0 and ! in_array($method, $this->only))
		{
			return false;
		}

		if (count($this->except) > 0 and in_array($method, $this->except))
		{
			return false;
		}

		$request = strtolower(Request::method());

		if (count($this->methods) > 0 and ! in_array($request, $this->methods))
		{
			return false;
		}

		return true;
	}

	/**
	 * Set the excluded controller methods.
	 *
	 * <code>
	 *		// Specify a filter for all methods except "index"
	 *		$this->filter('before', 'auth')->except('index');
	 *
	 *		// Specify a filter for all methods except "index" and "home"
	 *		$this->filter('before', 'auth')->except(array('index', 'home'));
	 * </code>
	 *
	 * @param  array              $methods
	 * @return Filter_Collection
	 */
	public function except($methods)
	{
		$this->except = (array) $methods;
		return $this;
	}

	/**
	 * Set the included controller methods.
	 *
	 * <code>
	 *		// Specify a filter for only the "index" method
	 *		$this->filter('before', 'auth')->only('index');
	 *
	 *		// Specify a filter for only the "index" and "home" methods
	 *		$this->filter('before', 'auth')->only(array('index', 'home'));
	 * </code>
	 *
	 * @param  array              $methods
	 * @return Filter_Collection
	 */
	public function only($methods)
	{
		$this->only = (array) $methods;
		return $this;
	}

	/**
	 * Set the HTTP methods for which the filter applies.
	 *
	 * <code>
	 *		// Specify that a filter only applies on POST requests
	 *		$this->filter('before', 'csrf')->on('post');
	 *
	 *		// Specify that a filter applies for multiple HTTP request methods
	 *		$this->filter('before', 'csrf')->on(array('post', 'put'));
	 * </code>
	 *
	 * @param  array              $methods
	 * @return Filter_Collection
	 */
	public function on($methods)
	{
		$this->methods = array_map('strtolower', (array) $methods);
		return $this;
	}

}