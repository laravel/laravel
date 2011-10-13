<?php namespace Laravel\Session;

use Laravel\Str;
use Laravel\Config;
use Laravel\Session\Drivers\Driver;
use Laravel\Session\Transporters\Transporter;

class Manager {

	/**
	 * The session driver instance.
	 *
	 * @var Driver
	 */
	private $driver;

	/**
	 * The session identifier transporter instance.
	 *
	 * @var Transporter
	 */
	private $transporter;

	/**
	 * Indicates if the session exists in persistent storage.
	 *
	 * @var bool
	 */
	private $exists = true;

	/**
	 * The current session payload.
	 *
	 * @var Payload
	 */
	public static $payload;

	/**
	 * Create a new session manager instance.
	 *
	 * @param  Driver       $driver
	 * @param  Transporter  $transporter
	 * @return void
	 */
	public function __construct(Driver $driver, Transporter $transporter)
	{
		$this->driver = $driver;
		$this->transporter = $transporter;
	}

	/**
	 * Get the session payload for the request.
	 *
	 * @param  array    $config
	 * @return Payload
	 */
	public function payload($config)
	{
		$session = $this->driver->load($this->transporter->get($config));

		// If the session is expired, a new session will be generated and all of
		// the data from the previous session will be lost. The new session will
		// be assigned a random, long string ID to uniquely identify it among
		// the application's current users.
		if (is_null($session) or (time() - $session['last_activity']) > ($config['lifetime'] * 60))
		{
			$this->exists = false;

			$session = array('id' => Str::random(40), 'data' => array());
		}

		$payload = new Payload($session);

		// If a CSRF token is not present in the session, we will generate one.
		// These tokens are generated per session to protect against Cross-Site
		// Request Forgery attacks on the application. It is up to the developer
		// to take advantage of them using the token methods on the Form class
		// and the "csrf" route filter.
		if ( ! $payload->has('csrf_token'))
		{
			$payload->put('csrf_token', Str::random(16));
		}

		return $payload;
	}

	/**
	 * Close the session handling for the request.
	 *
	 * @param  Payload  $payload
	 * @param  array    $config
	 * @param  array    $flash
	 * @return void
	 */
	public function close(Payload $payload, $config, $flash = array())
	{
		// If the session ID has been regenerated, we will need to inform the
		// session driver that the session will need to be persisted to the
		// data store as a new session.
		if ($payload->regenerated) $this->exists = false;

		foreach ($flash as $key => $value)
		{
			$payload->flash($key, $value);
		}

		$this->driver->save($payload->age(), $config, $this->exists);

		$this->transporter->put($payload->session['id'], $config);

		// Some session drivers may implement the Sweeper interface, meaning the
		// driver must do its garbage collection manually. Alternatively, some
		// drivers such as APC and Memcached are not required to manually
		// clean up their sessions.
		if (mt_rand(1, $config['sweepage'][1]) <= $config['sweepage'][0] and $this->driver instanceof Drivers\Sweeper)
		{
			$this->driver->sweep(time() - ($config['lifetime'] * 60));
		}
	}

	/**
	 * Dynamically pass methods to the current session payload.
	 *
	 * <code>
	 *		// Retrieve an item from the session payload
	 *		$name = Session::get('name');
	 *
	 *		// Write an item to the sessin payload
	 *		Session::put('name', 'Taylor');
	 * </code>
	 */
	public static function __callStatic($method, $parameters)
	{
		if ( ! is_null(static::$payload))
		{
			return call_user_func_array(array(static::$payload, $method), $parameters);
		}

		throw new \Exception("Call to undefined method [$method] on Session class.");
	}

}