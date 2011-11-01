<?php namespace Laravel\Session;

use Laravel\Str;
use Laravel\Config;
use Laravel\Cookie;
use Laravel\Session\Drivers\Driver;
use Laravel\Session\Drivers\Sweeper;

class Payload {

	/**
	 * The session array that is stored by the driver.
	 *
	 * @var array
	 */
	protected $session;

	/**
	 * Indicates if the session already exists in storage.
	 *
	 * @var bool
	 */
	protected $exists = true;

	/**
	 * The name of the session cookie used to store the session ID.
	 *
	 * @var string
	 */
	const cookie = 'laravel_session';

	/**
	 * Create a new session payload instance.
	 *
	 * @param  array  $session
	 * @return void
	 */
	public function __construct($session = null)
	{
		$this->session = $session;

		if ($this->invalid())
		{
			$this->exists = false;

			// A CSRF token is stored in every session. The token is used by the
			// Form class and the "csrf" filter to protect the application from
			// cross-site request forgery attacks. The token is simply a long,
			// random string which should be posted with each request.
			$token = Str::random(40);

			$this->session = array('id' => Str::random(40), 'data' => compact('token'));
		}
	}

	/**
	 * Deteremine if the session payload instance is valid.
	 *
	 * The session is considered valid if it exists and has not expired.
	 *
	 * @return bool
	 */
	protected function invalid()
	{
		$lifetime = Config::$items['session']['lifetime'];

		return is_null($this->session) or (time() - $this->last_activity > ($lifetime * 60));
	}

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
	 * The session flash data will also be checked for the requested item.
	 *
	 * <code>
	 *		// Get an item from the session
	 *		$name = Session::get('name');
	 *
	 *		// Return a default value if the item doesn't exist
	 *		$name = Session::get('name', 'Taylor');
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
			if (array_key_exists($possibility, $this->session['data']))
			{
				return $this->session['data'][$possibility];
			}
		}

		return ($default instanceof Closure) ? call_user_func($default) : $default;
	}

	/**
	 * Write an item to the session.
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
	 * Flash data only exists for the next request to the application.
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
	 * Keep all of the session flash data from expiring at the end of the request.
	 *
	 * @return void
	 */
	public function reflash()
	{
		$this->keep(array_keys($this->session['data']));
	}

	/**
	 * Keep a session flash item from expiring at the end of the request.
	 *
	 * @param  string|array  $key
	 * @return void
	 */
	public function keep($keys)
	{
		foreach ((array) $keys as $key) $this->flash($key, $this->get($key));
	}

	/**
	 * Remove an item from the session data.
	 *
	 * @param  string  $key
	 * @return Driver
	 */
	public function forget($key)
	{
		unset($this->session['data'][$key]);
	}

	/**
	 * Remove all of the items from the session.
	 *
	 * @return void
	 */
	public function flush()
	{
		$this->session['data'] = array();
	}

	/**
	 * Assign a new, random ID to the session.
	 *
	 * @return void
	 */
	public function regenerate()
	{
		$this->session['id'] = Str::random(40);

		$this->exists = false;
	}

	/**
	 * Store the session payload in storage.
	 *
	 * @param  Driver  $driver
	 * @return void
	 */
	public function save(Driver $driver)
	{
		$this->session['last_activity'] = time();

		$this->age();

		$config = Config::$items['session'];

		// To keep the session persistence code clean, session drivers are
		// responsible for the storage of the session array to the various
		// available persistent storage mechanisms.
		$driver->save($this->session, $config, $this->exists);

		$this->cookie();

		// Some session drivers implement the Sweeper interface, meaning that they
		// must clean up expired sessions manually. If the driver is a sweeper, we
		// need to determine if garbage collection should be run for the request.
		// Since garbage collection can be expensive, the probability of it
		// occuring is controlled by the "sweepage" configuration option.
		if ($driver instanceof Sweeper and (mt_rand(1, $config['sweepage'][1]) <= $config['sweepage'][0]))
		{
			$driver->sweep(time() - ($config['lifetime'] * 60));
		}
	}

	/**
	 * Age the session flash data.
	 *
	 * Session flash data is only available during the request in which it
	 * was flashed, and the request after that. To "age" the data, we will
	 * remove all of the :old: items and re-address the new items.
	 *
	 * @return void
	 */
	protected function age()
	{
		foreach ($this->session['data'] as $key => $value)
		{
			if (strpos($key, ':old:') === 0) $this->forget($key);
		}

		$this->replace(':new:', ':old:', array_keys($this->session['data']));
	}

	/**
	 * Re-address the session data by performing a string replacement on the keys.
	 *
	 * @param  string  $search
	 * @param  string  $replace
	 * @param  array   $keys
	 * @return void
	 */
	protected function replace($search, $replace, $keys)
	{
		$keys = str_replace($search, $replace, $keys);

		$this->session['data'] = array_combine($keys, array_values($this->session['data']));
	}

	/**
	 * Send the session ID cookie to the browser.
	 *
	 * @return void
	 */
	protected function cookie()
	{
		$config = Config::$items['session'];

		$minutes = ( ! $config['expire_on_close']) ? $config['lifetime'] : 0;
		
		Cookie::put(Payload::cookie, $this->id, $minutes, $config['path'], $config['domain'], $config['secure']);	
	}

	/**
	 * Dynamically retrieve items from the session array.
	 */
	public function __get($key)
	{
		return (isset($this->session[$key])) ? $this->session[$key] : $this->get($key);
	}

}