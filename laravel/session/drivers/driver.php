<?php namespace Laravel\Session\Drivers;

use Closure;
use Laravel\Str;
use Laravel\Input;
use Laravel\Config;
use Laravel\Cookie;

abstract class Driver {

	/**
	 * The session payload, containing the session ID, data and last activity timestamp.
	 *
	 * @var array
	 */
	public $session = array();

	/**
	 * The configuration manager instance.
	 *
	 * @var Config
	 */
	protected $config;

	/**
	 * Load the session for a given session ID.
	 *
	 * If the session has expired, a new, empty session will be generated.
	 *
	 * @param  Config  $config
	 * @param  string  $id
	 * @return void
	 */
	public final function start(Config $config, $id)
	{
		$this->config = $config;

		$this->session = ( ! is_null($id)) ? $this->load($id) : null;

		// If the session is expired, a new session will be generated and all of the data from
		// the previous session will be lost. The new session will be assigned a random, long
		// string ID to uniquely identify it among the application's current users.
		if (is_null($this->session) or $this->expired())
		{
			$this->session = array('id' => Str::random(40), 'last_activity' => time(), 'data' => array());
		}

		// If a CSRF token is not present in the session, we will generate one. These tokens
		// are generated per session to protect against Cross-Site Request Forgery attacks on
		// the application. It is up to the developer to take advantage of them using the token
		// methods on the Form class and the "csrf" route filter.
		if ( ! $this->has('csrf_token'))
		{
			$this->put('csrf_token', Str::random(16));
		}
	}

	/**
	 * Deteremine if the session is expired based on the last activity timestamp
	 * and the session lifetime set in the configuration file.
	 *
	 * @return bool
	 */
	private function expired()
	{
		return (time() - $this->session['last_activity']) > ($this->config->get('session.lifetime') * 60);
	}

	/**
	 * Load a session by ID.
	 *
	 * This method is responsible for retrieving the session from persistant storage. If the
	 * session does not exist in storage, nothing should be returned from the method, in which
	 * case a new session will be created by the base driver.
	 *
	 * @param  string  $id
	 * @return array
	 */
	abstract protected function load($id);

	/**
	 * Delete the session from persistant storage.
	 *
	 * @param  string  $id
	 * @return void
	 */
	abstract protected function delete($id);

	/**
	 * Save the session to persistant storage.
	 *
	 * @param  array  $session
	 * @return void
	 */
	abstract protected function save($session);

	/**
	 * Determine if the session or flash data contains an item.
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
	 * A default value may also be specified, and will be returned in the item doesn't exist.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public final function get($key, $default = null)
	{
		foreach (array($key, ':old:'.$key, ':new:'.$key) as $possibility)
		{
			if (array_key_exists($possibility, $this->session['data'])) return $this->session['data'][$possibility];
		}

		return ($default instanceof Closure) ? call_user_func($default) : $default;
	}

	/**
	 * Write an item to the session.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return Driver
	 */
	public final function put($key, $value)
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
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return Driver
	 */
	public final function flash($key, $value)
	{
		$this->put(':new:'.$key, $value);

		return $this;
	}

	/**
	 * Keep all of the session flash data from expiring at the end of the request.
	 *
	 * @return void
	 */
	public final function reflash()
	{
		$this->readdress(':old:', ':new:', array_keys($this->session['data']));
	}

	/**
	 * Keep a session flash item from expiring at the end of the request.
	 *
	 * If a string is passed to the method, only that item will be kept. An array may also
	 * be passed to the method, in which case all items in the array will be kept.
	 *
	 * @param  string|array  $key
	 * @return void
	 */
	public final function keep($key)
	{
		if (is_array($key)) return array_map(array($this, 'keep'), $key);

		$this->flash($key, $this->get($key));

		$this->forget(':old:'.$key);
	}

	/**
	 * Remove an item from the session.
	 *
	 * @param  string  $key
	 * @return Driver
	 */
	public final function forget($key)
	{
		unset($this->session['data'][$key]);
	}

	/**
	 * Remove all items from the session.
	 *
	 * @return void
	 */
	public final function flush()
	{
		$this->session['data'] = array();
	}

	/**
	 * Regenerate the session ID.
	 *
	 * @return void
	 */
	public final function regenerate()
	{
		$this->delete($this->session['id']);

		$this->session['id'] = Str::random(40);
	}

	/**
	 * Readdress the session data by performing a string replacement on the keys.
	 *
	 * @param  string  $search
	 * @param  string  $replace
	 * @param  array   $keys
	 * @return void
	 */
	private function readdress($search, $replace, $keys)
	{
		$this->session['data'] = array_combine(str_replace($search, $replace, $keys), array_values($this->session['data']));
	}

	/**
	 * Close the session and store the session payload in persistant storage.
	 *
	 * @param  Laravel\Input  $input
	 * @param  int            $time
	 * @return void
	 */
	public final function close(Input $input, $time)
	{
		// The input for the current request will be flashed to the session for
		// convenient access through the "old" method of the input class. This
		// allows the easy repopulation of forms.
		$this->flash('laravel_old_input', $input->get())->age();

		$this->session['last_activity'] = $time;

		$this->save($this->session);

		// Some session drivers implement the "Sweeper" interface, which specifies
		// that the driver needs to manually clean up its expired sessions. If the
		// driver does in fact implement this interface, we will randomly call the
		// sweep method on the driver.
		if ($this instanceof Sweeper and mt_rand(1, 100) <= 2)
		{
			$this->sweep($time - ($this->config->get('session.lifetime') * 60));
		}
	}

	/**
	 * Write the session cookie.
	 *
	 * @param  Laravel\Cookie  $cookie
	 * @param  array           $config
	 * @return void
	 */
	public final function cookie(Cookie $cookies)
	{
		if ( ! headers_sent())
		{
			$config = $this->config->get('session');

			extract($config);

			$minutes = ($expire_on_close) ? 0 : $lifetime;

			$cookies->put('laravel_session', $this->session['id'], $minutes, $path, $domain);
		}
	}

	/**
	 * Age the session flash data.
	 *
	 * @return void
	 */
	private function age()
	{
		// To age the data, we will forget all of the old keys and then rewrite the newly
		// flashed items to have old keys, which will be available for the next request.
		foreach ($this->session['data'] as $key => $value)
		{
			if (strpos($key, ':old:') === 0) $this->forget($key);
		}

		$this->readdress(':new:', ':old:', array_keys($this->session['data']));
	}

	/**
	 * Magic Method for retrieving items from the session.
	 */
	public function __get($key)
	{
		return $this->get($key);
	}

	/**
	 * Magic Method for writings items to the session.
	 */
	public function __set($key, $value)
	{
		$this->put($key, $value);
	}

}