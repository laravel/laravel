<?php namespace Illuminate\Routing;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

class UrlGenerator {

	/**
	 * The route collection.
	 *
	 * @var \Illuminate\Routing\RouteCollection
	 */
	protected $routes;

	/**
	 * The request instance.
	 *
	 * @var \Symfony\Component\HttpFoundation\Request
	 */
	protected $request;

	/**
	 * Characters that should not be URL encoded.
	 *
	 * @var array
	 */
	protected $dontEncode = array(
		'%2F' => '/',
		'%40' => '@',
		'%3A' => ':',
		'%3B' => ';',
		'%2C' => ',',
		'%3D' => '=',
		'%2B' => '+',
		'%21' => '!',
		'%2A' => '*',
		'%7C' => '|',
	);

	/**
	 * Create a new URL Generator instance.
	 *
	 * @param  \Illuminate\Routing\RouteCollection  $routes
	 * @param  \Symfony\Component\HttpFoundation\Request   $request
	 * @return void
	 */
	public function __construct(RouteCollection $routes, Request $request)
	{
		$this->routes = $routes;

		$this->setRequest($request);
	}

	/**
	 * Get the full URL for the current request.
	 *
	 * @return string
	 */
	public function full()
	{
		return $this->request->fullUrl();
	}

	/**
	 * Get the current URL for the request.
	 *
	 * @return string
	 */
	public function current()
	{
		return $this->to($this->request->getPathInfo());
	}

	/**
	 * Get the URL for the previous request.
	 *
	 * @return string
	 */
	public function previous()
	{
		return $this->to($this->request->headers->get('referer'));
	}

	/**
	 * Generate a absolute URL to the given path.
	 *
	 * @param  string  $path
	 * @param  mixed  $extra
	 * @param  bool  $secure
	 * @return string
	 */
	public function to($path, $extra = array(), $secure = null)
	{
		// First we will check if the URL is already a valid URL. If it is we will not
		// try to generate a new one but will simply return the URL as is, which is
		// convenient since developers do not always have to check if it's valid.
		if ($this->isValidUrl($path)) return $path;

		$scheme = $this->getScheme($secure);

		$tail = implode('/', array_map(
			'rawurlencode', (array) $extra)
		);

		// Once we have the scheme we will compile the "tail" by collapsing the values
		// into a single string delimited by slashes. This just makes it convenient
		// for passing the array of parameters to this URL as a list of segments.
		$root = $this->getRootUrl($scheme);

		return $this->trimUrl($root, $path, $tail);
	}

	/**
	 * Generate a secure, absolute URL to the given path.
	 *
	 * @param  string  $path
	 * @param  array   $parameters
	 * @return string
	 */
	public function secure($path, $parameters = array())
	{
		return $this->to($path, $parameters, true);
	}

	/**
	 * Generate a URL to an application asset.
	 *
	 * @param  string  $path
	 * @param  bool    $secure
	 * @return string
	 */
	public function asset($path, $secure = null)
	{
		if ($this->isValidUrl($path)) return $path;

		// Once we get the root URL, we will check to see if it contains an index.php
		// file in the paths. If it does, we will remove it since it is not needed
		// for asset paths, but only for routes to endpoints in the application.
		$root = $this->getRootUrl($this->getScheme($secure));

		return $this->removeIndex($root).'/'.trim($path, '/');
	}

	/**
	 * Remove the index.php file from a path.
	 *
	 * @param  string  $root
	 * @return string
	 */
	protected function removeIndex($root)
	{
		$i = 'index.php';

		return str_contains($root, $i) ? str_replace('/'.$i, '', $root) : $root;
	}

	/**
	 * Generate a URL to a secure asset.
	 *
	 * @param  string  $path
	 * @return string
	 */
	public function secureAsset($path)
	{
		return $this->asset($path, true);
	}

	/**
	 * Get the scheme for a raw URL.
	 *
	 * @param  bool    $secure
	 * @return string
	 */
	protected function getScheme($secure)
	{
		if (is_null($secure))
		{
			return $this->request->getScheme().'://';
		}
		else
		{
			return $secure ? 'https://' : 'http://';
		}
	}

	/**
	 * Get the URL to a named route.
	 *
	 * @param  string  $name
	 * @param  mixed   $parameters
	 * @param  bool  $absolute
	 * @param  \Illuminate\Routing\Route  $route
	 * @return string
	 *
	 * @throws \InvalidArgumentException
	 */
	public function route($name, $parameters = array(), $absolute = true, $route = null)
	{
		$route = $route ?: $this->routes->getByName($name);

		$parameters = (array) $parameters;

		if ( ! is_null($route))
		{
			return $this->toRoute($route, $parameters, $absolute);
		}
		else
		{
			throw new InvalidArgumentException("Route [{$name}] not defined.");
		}
	}

	/**
	 * Get the URL for a given route instance.
	 *
	 * @param  \Illuminate\Routing\Route  $route
	 * @param  array  $parameters
	 * @param  bool  $absolute
	 * @return string
	 */
	protected function toRoute($route, array $parameters, $absolute)
	{
		$domain = $this->getRouteDomain($route, $parameters);

		$uri = strtr(rawurlencode($this->trimUrl(
			$root = $this->replaceRoot($route, $domain, $parameters),
			$this->replaceRouteParameters($route->uri(), $parameters)
		)), $this->dontEncode).$this->getRouteQueryString($parameters);

		return $absolute ? $uri : '/'.ltrim(str_replace($root, '', $uri), '/');
	}

	/**
	 * Replace the parameters on the root path.
	 *
	 * @param  \Illuminate\Routing\Route  $route
	 * @param  string  $domain
	 * @param  array  $parameters
	 * @return string
	 */
	protected function replaceRoot($route, $domain, &$parameters)
	{
		return $this->replaceRouteParameters($this->getRouteRoot($route, $domain), $parameters);
	}

	/**
	 * Replace all of the wildcard parameters for a route path.
	 *
	 * @param  string  $path
	 * @param  array  $parameters
	 * @return string
	 */
	protected function replaceRouteParameters($path, array &$parameters)
	{
		if (count($parameters))
		{
			$path = preg_replace_sub(
				'/\{.*?\}/', $parameters, $this->replaceNamedParameters($path, $parameters)
			);
		}

		return trim(preg_replace('/\{.*?\?\}/', '', $path), '/');
	}

	/**
	 * Replace all of the named parameters in the path.
	 *
	 * @param  string  $path
	 * @param  array  $parameters
	 * @return string
	 */
	protected function replaceNamedParameters($path, &$parameters)
	{
		return preg_replace_callback('/\{(.*?)\??\}/', function($m) use (&$parameters)
		{
			return isset($parameters[$m[1]]) ? array_pull($parameters, $m[1]) : $m[0];

		}, $path);
	}

	/**
	 * Get the query string for a given route.
	 *
	 * @param  array  $parameters
	 * @return string
	 */
	protected function getRouteQueryString(array $parameters)
	{
		// First we will get all of the string parameters that are remaining after we
		// have replaced the route wildcards. We'll then build a query string from
		// these string parameters then use it as a starting point for the rest.
		if (count($parameters) == 0) return '';

		$query = http_build_query(
			$keyed = $this->getStringParameters($parameters)
		);

		// Lastly, if there are still parameters remaining, we will fetch the numeric
		// parameters that are in the array and add them to the query string or we
		// will make the initial query string if it wasn't started with strings.
		if (count($keyed) < count($parameters))
		{
			$query .= '&'.implode(
				'&', $this->getNumericParameters($parameters)
			);
		}

		return '?'.trim($query, '&');
	}

	/**
	 * Get the string parameters from a given list.
	 *
	 * @param  array  $parameters
	 * @return array
	 */
	protected function getStringParameters(array $parameters)
	{
		return array_where($parameters, function($k, $v) { return is_string($k); });
	}

	/**
	 * Get the numeric parameters from a given list.
	 *
	 * @param  array  $parameters
	 * @return array
	 */
	protected function getNumericParameters(array $parameters)
	{
		return array_where($parameters, function($k, $v) { return is_numeric($k); });
	}

	/**
	 * Get the formatted domain for a given route.
	 *
	 * @param  \Illuminate\Routing\Route  $route
	 * @param  array  $parameters
	 * @return string
	 */
	protected function getRouteDomain($route, &$parameters)
	{
		return $route->domain() ? $this->formatDomain($route, $parameters) : null;
	}

	/**
	 * Format the domain and port for the route and request.
	 *
	 * @param  \Illuminate\Routing\Route  $route
	 * @param  array  $parameters
	 * @return string
	 */
	protected function formatDomain($route, &$parameters)
	{
		return $this->addPortToDomain($this->getDomainAndScheme($route));
	}

	/**
	 * Get the domain and scheme for the route.
	 *
	 * @param  \Illuminate\Routing\Route  $route
	 * @return string
	 */
	protected function getDomainAndScheme($route)
	{
		return $this->getRouteScheme($route).$route->domain();
	}

	/**
	 * Add the port to the domain if necessary.
	 *
	 * @param  string  $domain
	 * @return string
	 */
	protected function addPortToDomain($domain)
	{
		if (in_array($this->request->getPort(), array('80', '443')))
		{
			return $domain;
		}
		else
		{
			return $domain .= ':'.$this->request->getPort();
		}
	}

	/**
	 * Get the root of the route URL.
	 *
	 * @param  \Illuminate\Routing\Route  $route
	 * @param  string  $domain
	 * @return string
	 */
	protected function getRouteRoot($route, $domain)
	{
		return $this->getRootUrl($this->getRouteScheme($route), $domain);
	}

	/**
	 * Get the scheme for the given route.
	 *
	 * @param  \Illuminate\Routing\Route  $route
	 * @return string
	 */
	protected function getRouteScheme($route)
	{
		if ($route->httpOnly())
		{
			return $this->getScheme(false);
		}
		elseif ($route->httpsOnly())
		{
			return $this->getScheme(true);
		}
		else
		{
			return $this->getScheme(null);
		}
	}

	/**
	 * Get the URL to a controller action.
	 *
	 * @param  string  $action
	 * @param  mixed   $parameters
	 * @param  bool    $absolute
	 * @return string
	 */
	public function action($action, $parameters = array(), $absolute = true)
	{
		return $this->route($action, $parameters, $absolute, $this->routes->getByAction($action));
	}

	/**
	 * Get the base URL for the request.
	 *
	 * @param  string  $scheme
	 * @param  string  $root
	 * @return string
	 */
	protected function getRootUrl($scheme, $root = null)
	{
		$root = $root ?: $this->request->root();

		$start = starts_with($root, 'http://') ? 'http://' : 'https://';

		return preg_replace('~'.$start.'~', $scheme, $root, 1);
	}

	/**
	 * Determine if the given path is a valid URL.
	 *
	 * @param  string  $path
	 * @return bool
	 */
	public function isValidUrl($path)
	{
		if (starts_with($path, array('#', '//', 'mailto:', 'tel:'))) return true;

		return filter_var($path, FILTER_VALIDATE_URL) !== false;
	}

	/**
	 * Format the given URL segments into a single URL.
	 *
	 * @param  string  $root
	 * @param  string  $path
	 * @param  string  $tail
	 * @return string
	 */
	protected function trimUrl($root, $path, $tail = '')
	{
		return trim($root.'/'.trim($path.'/'.$tail, '/'), '/');
	}

	/**
	 * Get the request instance.
	 *
	 * @return \Symfony\Component\HttpFoundation\Request
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * Set the current request instance.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Request  $request
	 * @return void
	 */
	public function setRequest(Request $request)
	{
		$this->request = $request;
	}

}
