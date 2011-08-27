<?php namespace Laravel\Session;

use Laravel\Str;
use Laravel\Input;
use Laravel\Cookie;

abstract class Driver {

	/**
	 * The session payload, which contains the session ID, data and last activity timestamp.
	 *
	 * @var array
	 */
	public $session = array();

	/**
	 * Load the session for a given session ID.
	 *
	 * The session will be checked for validity and necessary data. For example, if the session
	 * does not have a CSRF token, a token will be generated for the session.
	 *
	 * If the session has expired, a new, empty session will be generated.
	 *
	 * @param  string  $id
	 * @param  int     $lifetime
	 * @return void
	 */
	public function start($id, $lifetime)
	{
		$this->session = ( ! is_null($id)) ? $this->load($id) : null;

		if (is_null($this->session) or (time() - $this->session['last_activity']) > ($lifetime * 60))
		{
			$this->session = array('id' => Str::random(40), 'data' => array());
		}

		if ( ! $this->has('csrf_token')) $this->put('csrf_token', Str::random(16));

		$this->session['last_activity'] = time();
	}

	/**
	 * Load a session by ID.
	 *
	 * The session will be retrieved from persistant storage and returned as an array.
	 * The array contains the session ID, last activity UNIX timestamp, and session data.
	 *
	 * @param  string  $id
	 * @return array
	 */
	abstract protected function load($id);

	/**
	 * Delete the session from persistant storage.
	 *
	 * @return void
	 */
	abstract protected function delete();

	/**
	 * Save the session to persistant storage.
	 *
	 * @return void
	 */
	abstract protected function save();

	/**
	 * Determine if the session or flash data contains an item.
	 *
	 * <code>
	 *		// Determine if "name" item exists in the session
	 *		$exists = Session::driver()->has('name');
	 * </code>
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function has($key)
	{
		return ( ! is_null($this->get($key)));
	}

	/**
	 * Get an item from the session.
	 *
	 * A default value may also be specified, and will be returned in the requested
	 * item does not exist in the session.
	 *
	 * <code>
	 *		// Get the "name" item from the session
	 *		$name = Session::driver()->get('name');
	 *
	 *		// Get the "name" item from the session or return "Fred"
	 *		$name = Session::driver()->get('name', 'Fred');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public function get($key, $default = null)
	{
		foreach (array($key, ':old:'.$key, ':new:'.$key) as $possibility)
		{
			if (array_key_exists($possibility, $this->session['data'])) return $this->session['data'][$possibility];
		}

		return ($default instanceof \Closure) ? call_user_func($default) : $default;
	}

	/**
	 * Write an item to the session.
	 *
	 * <code>
	 *		// Write the "name" item to the session
	 *		Session::driver()->put('name', 'Fred');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return Driver
	 */
	public function put($key, $value)
	{
		$this->session['data'][$key] = $value;

		return $this;
	}

	/**
	 * Write an item to the session flash data.
	 *
	 * Flash data only exists for the next request. After that, it will be removed from
	 * the session. Flash data is useful for temporary status or welcome messages.
	 *
	 * <code>
	 *		// Write the "name" item to the session flash data
	 *		Session::driver()->flash('name', 'Fred');
	 * </code>
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return Driver
	 */
	public function flash($key, $value)
	{
		$this->put(':new:'.$key, $value);

		return $this;
	}

	/**
	 * Remove an item from the session.
	 *
	 * <code>
	 *		// Remove the "name" item from the session
	 *		Session::driver()->forget('name');
	 * </code>
	 *
	 * @param  string  $key
	 * @return Driver
	 */
	public function forget($key)
	{
		unset($this->session['data'][$key]);
	}

	/**
	 * Remove all items from the session.
	 *
	 * @return void
	 */
	public function flush()
	{
		$this->session['data'] = array();
	}

	/**
	 * Regenerate the session ID.
	 *
	 * @return void
	 */
	public function regenerate()
	{
		$this->delete();

		$this->session['id'] = Str::random(40);
	}

	/**
	 * Close the session.
	 *
	 * The session will be stored in persistant storage and the session cookie will be
	 * session cookie will be sent to the browser.
	 *
	 * The input of the current request will also be flashed to the session so it is
	 * available for the next request via the "old" method on the input class.
	 *
	 * @param  Laravel\Input  $input
	 * @param  array          $config
	 * @return void
	 */
	public function close(Input $input, $config)
	{
		$this->flash('laravel_old_input', $input->get())->age();

		$this->save();

		$this->write_cookie($input->cookies, $config);

		if ($this instanceof Sweeper and mt_rand(1, 100) <= 2)
		{
			$this->sweep(time() - ($config['lifetime'] * 60));
		}
	}

	/**
	 * Age the session flash data.
	 *
	 * To age the data, we will forget all of the old keys and then rewrite the newly
	 * flashed items to have old keys, which will be available for the next request.
	 *
	 * @return void
	 */
	protected function age()
	{
		foreach ($this->session['data'] as $key => $value)
		{
			if (strpos($key, ':old:') === 0) $this->forget($key);
		}

		$session = $this->session['data'];

		$this->session['data'] = array_combine(str_replace(':new:', ':old:', array_keys($session)), array_values($session));
	}

	/**
	 * Write the session cookie.
	 *
	 * All of the session cookie configuration options are stored in the session
	 * configuration file. The cookie will only be written if the headers have not
	 * already been sent to the browser.
	 *
	 * @param  Laravel\Cookie  $cookie
	 * @param  array           $config
	 * @return void
	 */
	protected function write_cookie(Cookie $cookies, $config)
	{
		if ( ! headers_sent())
		{
			extract($config);

			$minutes = ($expire_on_close) ? 0 : $lifetime;

			$cookies->put('laravel_session', $this->session['id'], $minutes, $path, $domain, $https, $http_only);
		}
	}

	/**
	 * Magic Method for retrieving items from the session.
	 *
	 * <code>
	 *		// Get the "name" item from the session
	 *		$name = $application->session->name;
	 * </code>
	 */
	public function __get($key)
	{
		return $this->get($key);
	}

	/**
	 * Magic Method for writings items to the session.
	 *
	 * <code>
	 *		// Write "Fred" to the session "name" item
	 *		$application->session->name = 'Fred';
	 * </code>
	 */
	public function __set($key, $value)
	{
		$this->put($key, $value);
	}

}