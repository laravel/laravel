<?php namespace Laravel\Session;

use Closure;
use Laravel\Str;

class Payload {

	/**
	 * The raw session payload array.
	 *
	 * @var array
	 */
	public $session = array();

	/**
	 * Create a new session container instance.
	 *
	 * @param  array  $session
	 * @return void
	 */
	public function __construct($session)
	{
		$this->session = $session;
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
	 * A default value may also be specified, and will be returned in the item doesn't exist.
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
	 * Keep all of the session flash data from expiring at the end of the request.
	 *
	 * @return void
	 */
	public function reflash()
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
	public function keep($key)
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
		$this->session['id'] = Str::random(40);
	}

	/**
	 * Age the session payload, preparing it for storage after a request.
	 *
	 * The session flash data will be aged and the last activity timestamp will be updated.
	 * The aged session array will be returned by the method.
	 *
	 * @return array
	 */
	public function age()
	{
		$this->session['last_activity'] = time();

		// To age the data, we will forget all of the old keys and then rewrite the newly
		// flashed items to have old keys, which will be available for the next request.
		foreach ($this->session['data'] as $key => $value)
		{
			if (strpos($key, ':old:') === 0) $this->forget($key);
		}

		$this->readdress(':new:', ':old:', array_keys($this->session['data']));

		return $this->session;
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

}