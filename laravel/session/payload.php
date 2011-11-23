<?php namespace Laravel\Session;

use Closure;
use Laravel\Arr;
use Laravel\Str;
use Laravel\Config;
use Laravel\Cookie;
use Laravel\Session\Drivers\Driver;
use Laravel\Session\Drivers\Sweeper;

if (Config::$items['application']['key'] === '')
{
	throw new \LogicException("An application key is required to use sessions.");
}

class Payload {

	/**
	 * The session array that is stored by the driver.
	 *
	 * @var array
	 */
	public $session;

	/**
	 * Indicates if the session already exists in storage.
	 *
	 * @var bool
	 */
	protected $exists = true;

	/**
	 * The session driver used to retrieve and store the session payload.
	 *
	 * @var Driver
	 */
	protected $driver;

	/**
	 * The string name of the CSRF token stored in the session.
	 *
	 * @var string
	 */
	const token = 'csrf_token';

	/**
	 * Create a new session payload instance.
	 *
	 * @param  Driver  $driver
	 * @return void
	 */
	public function __construct(Driver $driver)
	{
		$this->driver = $driver;
	}

	/**
	 * Load the session for the current request.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public function load($id)
	{
		if ( ! is_null($id)) $this->session = $this->driver->load($id);

		// If the session doesn't exist or is invalid, we will create a new session
		// array and mark the session as being non-existent. Some drivers, such as
		// the database driver, need to know whether the session exists in storage
		// so they can know whether to "insert" or "update" the session.
		if (is_null($this->session) or $this->invalid())
		{
			$this->exists = false;

			$this->session = array('id' => Str::random(40), 'data' => array(
				':new:' => array(),
				':old:' => array(),
			));
		}

		// A CSRF token is stored in every session. The token is used by the Form
		// class and the "csrf" filter to protect the application from cross-site
		// request forgery attacks. The token is simply a long, random string
		// which should be posted with each request.
		if ( ! $this->has(Payload::token))
		{
			$this->put(Payload::token, Str::random(40));
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

		return (time() - $this->session['last_activity']) > ($lifetime * 60);
	}

	/**
	 * Determine if session handling has been started for the request.
	 *
	 * @return bool
	 */
	public function started()
	{
		return is_array($this->session);
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
		if (isset($this->session['data'][$key]))
		{
			return $this->session['data'][$key];
		}
		elseif (isset($this->session['data'][':new:'][$key]))
		{
			return $this->session['data'][':new:'][$key];
		}
		elseif (isset($this->session['data'][':old:'][$key]))
		{
			return $this->session['data'][':old:'][$key];
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
		Arr::set($this->session['data'], $key, $value);
	}

	/**
	 * Write an item to the session flash data.
	 *
	 * Flash data only exists for the next request to the application, and is
	 * useful for storing temporary data such as error or status messages.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function flash($key, $value)
	{
		Arr::set($this->session['data'][':new:'], $key, $value);
	}

	/**
	 * Keep the session flash data from expiring at the end of the request.
	 *
	 * @return void
	 */
	public function reflash()
	{
		$old = $this->session['data'][':old:'];

		$this->session['data'][':new:'] = array_merge($this->session['data'][':new:'], $old);
	}

	/**
	 * Keep a session flash item from expiring at the end of the request.
	 *
	 * @param  string|array  $key
	 * @return void
	 */
	public function keep($keys)
	{
		foreach ((array) $keys as $key)
		{
			$this->flash($key, $this->get($key));
		}
	}

	/**
	 * Remove an item from the session data.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		Arr::forget($this->session['data'], $key);
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
	 * Get the CSRF token that is stored in the session data.
	 *
	 * @return string
	 */
	public function token()
	{
		return $this->get(Payload::token);
	}

	/**
	 * Store the session payload in storage.
	 *
	 * The activity timestamp will be set, the flash data will be aged, and the
	 * session cookie will be written. The driver given when the session payload
	 * was constructed will be used to persist the session to storage.
	 *
	 * If the session's driver is a sweeper implementation, garbage collection
	 * may be performed based on the probabilities set in the "sweepage" option
	 * in the session configuration file.
	 *
	 * @return void
	 */
	public function save()
	{
		$this->session['last_activity'] = time();

		$this->age();

		$config = Config::$items['session'];

		$this->driver->save($this->session, $config, $this->exists);

		$this->cookie();

		// Some session drivers implement the Sweeper interface, meaning that they
		// must clean up expired sessions manually. If the driver is a sweeper, we
		// need to determine if garbage collection should be run for the request.
		// Since garbage collection can be expensive, the probability of it
		// occuring is controlled by the "sweepage" configuration option.
		$sweepage = $config['sweepage'];

		if ($this->driver instanceof Sweeper and (mt_rand(1, $sweepage[1]) <= $sweepage[0]))
		{
			$this->driver->sweep(time() - ($config['lifetime'] * 60));
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
		$this->session['data'][':old:'] = $this->session['data'][':new:'];

		$this->session['data'][':new:'] = array();
	}

	/**
	 * Send the session ID cookie to the browser.
	 *
	 * @return void
	 */
	protected function cookie()
	{
		$config = Config::$items['session'];

		extract($config, EXTR_SKIP);

		$minutes = ( ! $expire_on_close) ? $lifetime : 0;

		Cookie::put($cookie, $this->session['id'], $minutes, $path, $domain, $secure);	
	}

}