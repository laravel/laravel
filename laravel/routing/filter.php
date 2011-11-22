<?php namespace Laravel\Routing;

use Laravel\Request;

class Filter {

	/**
	 * The route filters for the application.
	 *
	 * @var array
	 */
	protected static $filters = array();

	/**
	 * Register an array of route filters.
	 *
	 * @param  array  $filters
	 * @return void
	 */
	public static function register($filters)
	{
		static::$filters = array_merge(static::$filters, $filters);
	}

	/**
	 * Call a filter or set of filters.
	 *
	 * @param  array|string  $filters
	 * @param  array         $parameters
	 * @param  bool          $override
	 * @return mixed
	 */
	public static function run($filters, $parameters = array(), $override = false)
	{
		foreach (static::parse($filters) as $filter)
		{
			// Parameters may be passed into routes by specifying the list of
			// parameters after a colon. If parameters are present, we will
			// merge them into the parameter array that was passed to the
			// method and slice the parameters off of the filter string.
			if (($colon = strpos($filter, ':')) !== false)
			{
				$parameters = array_merge($parameters, explode(',', substr($filter, $colon + 1)));

				$filter = substr($filter, 0, $colon);
			}

			if ( ! isset(static::$filters[$filter])) continue;

			$response = call_user_func_array(static::$filters[$filter], $parameters);

			// "Before" filters may override the request cycle. For example,
			// an authentication filter may redirect a user to a login view
			// if they are not logged in. Because of this, we will return
			// the first filter response if overriding is enabled.
			if ( ! is_null($response) and $override) return $response;
		}
	}

	/**
	 * Parse a string of filters into an array.
	 *
	 * @param  string|array  $filters
	 * @return array
	 */
	public static function parse($filters)
	{
		return (is_string($filters)) ? explode('|', $filters) : (array) $filters;
	}

}

class Filter_Collection {

	/**
	 * The event being filtered.
	 *
	 * @var string
	 */
	public $name;

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
	 * The filters contained by the collection.
	 *
	 * @var string|array
	 */
	public $filters = array();

	/**
	 * The HTTP methods for which the filter applies.
	 *
	 * @var array
	 */
	public $methods = array();

	/**
	 * Create a new filter collection instance.
	 *
	 * @param  string        $name
	 * @param  string|array  $filters
	 */
	public function __construct($name, $filters)
	{
		$this->name = $name;
		$this->filters = Filter::parse($filters);
	}

	/**
	 * Determine if this collection's filters apply to a given method.
	 *
	 * Methods may be included / excluded using the "only" and "except" methods on the
	 * filter collection. Also, the "on" method may be used to set certain filters to
	 * only run when the request uses a given HTTP verb.
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

		if (count($this->methods) > 0 and ! in_array(strtolower(Request::method()), $this->methods))
		{
			return false;
		}

		return true;
	}

	/**
	 * Set the excluded controller methods.
	 *
	 * When methods are excluded, the collection's filters will be run for each
	 * controller method except those explicitly specified via this method.
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
	 * This method is the inverse of the "except" methods. The methods specified
	 * via this method are the only controller methods on which the collection's
	 * filters will be run.
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
	 * Since some filters, such as the CSRF filter, only make sense in a POST
	 * request context, this method allows you to limit which HTTP methods
	 * the filter will apply to.
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