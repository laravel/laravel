<?php namespace Laravel\Session;

use Laravel\Str;
use Laravel\Config;
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
	 * @return void
	 */
	public function start($id)
	{
		$this->session = ( ! is_null($id)) ? $this->load($id) : null;

		if (is_null($this->session) or (time() - $this->session['last_activity']) > (Config::get('session.lifetime') * 60))
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
	 * @return void
	 */
	public function put($key, $value)
	{
		$this->session['data'][$key] = $value;
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
	 * @return void
	 */
	public function flash($key, $value)
	{
		$this->put(':new:'.$key, $value);
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
	 * @return void
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
	 * @param  Laravel\Cookie  $cookie
	 * @return void
	 */
	public function close(\Laravel\Cookie $cookie)
	{
		$this->age_flash();

		$this->save();

		$this->write_cookie($cookie);
	}

	/**
	 * Age the session flash data.
	 *
	 * @return void
	 */
	public function age_flash()
	{
		foreach ($this->session['data'] as $key => $value)
		{
			if (strpos($key, ':old:') === 0) $this->forget($key);
		}

		foreach ($this->session['data'] as $key => $value)
		{
			if (strpos($key, ':new:') === 0)
			{
				$this->put(':old:'.substr($key, 5), $value);

				$this->forget($key);
			}
		}
	}

	/**
	 * Write the session cookie.
	 *
	 * @param  Laravel\Cookie  $cookie
	 * @return void
	 */
	protected function write_cookie(\Laravel\Cookie $cookie)
	{
		if ( ! headers_sent())
		{
			$config = Config::get('session');

			extract($config);

			$minutes = ($expire_on_close) ? 0 : $lifetime;

			$cookie->put('laravel_session', $this->session['id'], $minutes, $path, $domain, $https, $http_only);
		}
	}

}