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
	 * The session payload instance.
	 *
	 * @var Payload
	 */
	private $payload;

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

		// If the session is expired, a new session will be generated and all of the data from
		// the previous session will be lost. The new session will be assigned a random, long
		// string ID to uniquely identify it among the application's current users.
		if (is_null($session) or $this->expired($session, $config))
		{
			$session = array('id' => Str::random(40), 'data' => array());
		}

		$payload = new Payload($session);

		// If a CSRF token is not present in the session, we will generate one. These tokens
		// are generated per session to protect against Cross-Site Request Forgery attacks on
		// the application. It is up to the developer to take advantage of them using the token
		// methods on the Form class and the "csrf" route filter.
		if ( ! $payload->has('csrf_token'))
		{
			$payload->put('csrf_token', Str::random(16));
		}

		return $payload;
	}

	/**
	 * Deteremine if the session is expired based on the last activity timestamp
	 * and the session lifetime set in the configuration file.
	 *
	 * @param  array  $session
	 * @param  array  $config
	 * @return bool
	 */
	private function expired($session, $config)
	{
		return (time() - $session['last_activity']) > ($config['lifetime'] * 60);
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
		foreach ($flash as $key => $value)
		{
			$this->driver->flash($key, $value);
		}

		$this->driver->save($payload->age(), $config);

		$this->transporter->put($payload->session['id'], $config);

		// Some session drivers implement the Sweeper interface, which specified that the driver
		// must do its garbage collection manually. Alternatively, some drivers such as APC and
		// Memcached are not required to manually clean up their sessions.
		if (mt_rand(1, $config['sweepage'][1]) <= $config['sweepage'][0] and $this->driver instanceof Drivers\Sweeper)
		{
			$this->driver->sweep(time() - ($config['lifetime'] * 60));
		}
	}

}