<?php namespace Illuminate\Http;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request extends SymfonyRequest {

	/**
	 * The decoded JSON content for the request.
	 *
	 * @var string
	 */
	protected $json;

	/**
	 * The Illuminate session store implementation.
	 *
	 * @var \Illuminate\Session\Store
	 */
	protected $sessionStore;

	/**
	 * Return the Request instance.
	 *
	 * @return \Illuminate\Http\Request
	 */
	public function instance()
	{
		return $this;
	}

	/**
	 * Get the request method.
	 *
	 * @return string
	 */
	public function method()
	{
		return $this->getMethod();
	}

	/**
	 * Get the root URL for the application.
	 *
	 * @return string
	 */
	public function root()
	{
		return rtrim($this->getSchemeAndHttpHost().$this->getBaseUrl(), '/');
	}

	/**
	 * Get the URL (no query string) for the request.
	 *
	 * @return string
	 */
	public function url()
	{
		return rtrim(preg_replace('/\?.*/', '', $this->getUri()), '/');
	}

	/**
	 * Get the full URL for the request.
	 *
	 * @return string
	 */
	public function fullUrl()
	{
		$query = $this->getQueryString();

		return $query ? $this->url().'?'.$query : $this->url();
	}

	/**
	 * Get the current path info for the request.
	 *
	 * @return string
	 */
	public function path()
	{
		$pattern = trim($this->getPathInfo(), '/');

		return $pattern == '' ? '/' : $pattern;
	}

	/**
	 * Get the current encoded path info for the request.
	 *
	 * @return string
	 */
	public function decodedPath()
	{
		return rawurldecode($this->path());
	}

	/**
	 * Get a segment from the URI (1 based index).
	 *
	 * @param  string  $index
	 * @param  mixed   $default
	 * @return string
	 */
	public function segment($index, $default = null)
	{
		return array_get($this->segments(), $index - 1, $default);
	}

	/**
	 * Get all of the segments for the request path.
	 *
	 * @return array
	 */
	public function segments()
	{
		$segments = explode('/', $this->path());

		return array_values(array_filter($segments, function($v) { return $v != ''; }));
	}

	/**
	 * Determine if the current request URI matches a pattern.
	 *
	 * @param  dynamic  string
	 * @return bool
	 */
	public function is()
	{
		foreach (func_get_args() as $pattern)
		{
			if (str_is($pattern, urldecode($this->path())))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Determine if the request is the result of an AJAX call.
	 *
	 * @return bool
	 */
	public function ajax()
	{
		return $this->isXmlHttpRequest();
	}

	/**
	 * Determine if the request is over HTTPS.
	 *
	 * @return bool
	 */
	public function secure()
	{
		return $this->isSecure();
	}

	/**
	 * Determine if the request contains a given input item key.
	 *
	 * @param  string|array  $key
	 * @return bool
	 */
	public function exists($key)
	{
		$keys = is_array($key) ? $key : func_get_args();

		$input = $this->all();

		foreach ($keys as $value)
		{
			if ( ! array_key_exists($value, $input)) return false;
		}

		return true;
	}

	/**
	 * Determine if the request contains a non-emtpy value for an input item.
	 *
	 * @param  string|array  $key
	 * @return bool
	 */
	public function has($key)
	{
		$keys = is_array($key) ? $key : func_get_args();

		foreach ($keys as $value)
		{
			if ($this->isEmptyString($value)) return false;
		}

		return true;
	}

	/**
	 * Determine if the given input key is an empty string for "has".
	 *
	 * @param  string  $key
	 * @return bool
	 */
	protected function isEmptyString($key)
	{
		$boolOrArray = is_bool($this->input($key)) || is_array($this->input($key));

		return ! $boolOrArray && trim((string) $this->input($key)) === '';
	}

	/**
	 * Get all of the input and files for the request.
	 *
	 * @return array
	 */
	public function all()
	{
		return array_merge_recursive($this->input(), $this->files->all());
	}

	/**
	 * Retrieve an input item from the request.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return string
	 */
	public function input($key = null, $default = null)
	{
		$input = $this->getInputSource()->all() + $this->query->all();

		return array_get($input, $key, $default);
	}

	/**
	 * Get a subset of the items from the input data.
	 *
	 * @param  array  $keys
	 * @return array
	 */
	public function only($keys)
	{
		$keys = is_array($keys) ? $keys : func_get_args();

		return array_only($this->input(), $keys) + array_fill_keys($keys, null);
	}

	/**
	 * Get all of the input except for a specified array of items.
	 *
	 * @param  array  $keys
	 * @return array
	 */
	public function except($keys)
	{
		$keys = is_array($keys) ? $keys : func_get_args();

		$results = $this->input();

		foreach ($keys as $key) array_forget($results, $key);

		return $results;
	}

	/**
	 * Retrieve a query string item from the request.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return string
	 */
	public function query($key = null, $default = null)
	{
		return $this->retrieveItem('query', $key, $default);
	}

	/**
	 * Determine if a cookie is set on the request.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function hasCookie($key)
	{
		return ! is_null($this->cookie($key));
	}

	/**
	 * Retrieve a cookie from the request.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return string
	 */
	public function cookie($key = null, $default = null)
	{
		return $this->retrieveItem('cookies', $key, $default);
	}

	/**
	 * Retrieve a file from the request.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return \Symfony\Component\HttpFoundation\File\UploadedFile|array
	 */
	public function file($key = null, $default = null)
	{
		return array_get($this->files->all(), $key, $default);
	}

	/**
	 * Determine if the uploaded data contains a file.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function hasFile($key)
	{
		if (is_array($file = $this->file($key))) $file = head($file);

		return $file instanceof \SplFileInfo && $file->getPath() != '';
	}

	/**
	 * Retrieve a header from the request.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return string
	 */
	public function header($key = null, $default = null)
	{
		return $this->retrieveItem('headers', $key, $default);
	}

	/**
	 * Retrieve a server variable from the request.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return string
	 */
	public function server($key = null, $default = null)
	{
		return $this->retrieveItem('server', $key, $default);
	}

	/**
	 * Retrieve an old input item.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public function old($key = null, $default = null)
	{
		return $this->session()->getOldInput($key, $default);
	}

	/**
	 * Flash the input for the current request to the session.
	 *
	 * @param  string $filter
	 * @param  array  $keys
	 * @return void
	 */
	public function flash($filter = null, $keys = array())
	{
		$flash = ( ! is_null($filter)) ? $this->$filter($keys) : $this->input();

		$this->session()->flashInput($flash);
	}

	/**
	 * Flash only some of the input to the session.
	 *
	 * @param  dynamic  string
	 * @return void
	 */
	public function flashOnly($keys)
	{
		$keys = is_array($keys) ? $keys : func_get_args();

		return $this->flash('only', $keys);
	}

	/**
	 * Flash only some of the input to the session.
	 *
	 * @param  dynamic  string
	 * @return void
	 */
	public function flashExcept($keys)
	{
		$keys = is_array($keys) ? $keys : func_get_args();

		return $this->flash('except', $keys);
	}

	/**
	 * Flush all of the old input from the session.
	 *
	 * @return void
	 */
	public function flush()
	{
		$this->session()->flashInput(array());
	}

	/**
	 * Retrieve a parameter item from a given source.
	 *
	 * @param  string  $source
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return string
	 */
	protected function retrieveItem($source, $key, $default)
	{
		if (is_null($key))
		{
			return $this->$source->all();
		}
		else
		{
			return $this->$source->get($key, $default, true);
		}
	}

	/**
	 * Merge new input into the current request's input array.
	 *
	 * @param  array  $input
	 * @return void
	 */
	public function merge(array $input)
	{
		$this->getInputSource()->add($input);
	}

	/**
	 * Replace the input for the current request.
	 *
	 * @param  array  $input
	 * @return void
	 */
	public function replace(array $input)
	{
		$this->getInputSource()->replace($input);
	}

	/**
	 * Get the JSON payload for the request.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public function json($key = null, $default = null)
	{
		if ( ! isset($this->json))
		{
			$this->json = new ParameterBag((array) json_decode($this->getContent(), true));
		}

		if (is_null($key)) return $this->json;

		return array_get($this->json->all(), $key, $default);
	}

	/**
	 * Get the input source for the request.
	 *
	 * @return \Symfony\Component\HttpFoundation\ParameterBag
	 */
	protected function getInputSource()
	{
		if ($this->isJson()) return $this->json();

		return $this->getMethod() == 'GET' ? $this->query : $this->request;
	}

	/**
	 * Determine if the request is sending JSON.
	 *
	 * @return bool
	 */
	public function isJson()
	{
		return str_contains($this->header('CONTENT_TYPE'), '/json');
	}

	/**
	 * Determine if the current request is asking for JSON in return.
	 *
	 * @return bool
	 */
	public function wantsJson()
	{
		$acceptable = $this->getAcceptableContentTypes();

		return isset($acceptable[0]) && $acceptable[0] == 'application/json';
	}

	/**
	 * Get the data format expected in the response.
	 *
	 * @return string
	 */
	public function format($default = 'html')
	{
		foreach ($this->getAcceptableContentTypes() as $type)
		{
			if ($format = $this->getFormat($type)) return $format;
		}

		return $default;
	}

	/**
	 * Create an Illuminate request from a Symfony instance.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Request  $request
	 * @return \Illuminate\Http\Request
	 */
	public static function createFromBase(SymfonyRequest $request)
	{
		if ($request instanceof static) return $request;

		return with(new static)->duplicate(

			$request->query->all(), $request->request->all(), $request->attributes->all(),

			$request->cookies->all(), $request->files->all(), $request->server->all()
		);
	}

	/**
	 * Get the session associated with the request.
	 *
	 * @return \Illuminate\Session\Store
	 *
	 * @throws \RuntimeException
	 */
	public function session()
	{
		if ( ! $this->hasSession())
		{
			throw new \RuntimeException("Session store not set on request.");
		}

		return $this->getSession();
	}

}
