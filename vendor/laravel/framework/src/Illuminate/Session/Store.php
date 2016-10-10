<?php namespace Illuminate\Session;

use SessionHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;

class Store implements SessionInterface {

	/**
	 * The session ID.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * The session name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * The session attributes.
	 *
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * The session bags.
	 *
	 * @var array
	 */
	protected $bags = array();

	/**
	 * The meta-data bag instance.
	 *
	 * @var \Symfony\Component\Session\Storage\MetadataBag
	 */
	protected $metaBag;

	/**
	 * Local copies of the session bag data.
	 *
	 * @var array
	 */
	protected $bagData = array();

	/**
	 * The session handler implementation.
	 *
	 * @var \SessionHandlerInterface
	 */
	protected $handler;

	/**
	 * Session store started status.
	 *
	 * @var bool
	 */
	protected $started = false;

	/**
	 * Create a new session instance.
	 *
	 * @param  string  $name
	 * @param  \SessionHandlerInterface  $handler
	 * @param  string|null $id
	 * @return void
	 */
	public function __construct($name, SessionHandlerInterface $handler, $id = null)
	{
		$this->name = $name;
		$this->handler = $handler;
		$this->metaBag = new MetadataBag;
		$this->setId($id ?: $this->generateSessionId());
	}

	/**
	 * {@inheritdoc}
	 */
	public function start()
	{
		$this->loadSession();

		if ( ! $this->has('_token')) $this->regenerateToken();

		return $this->started = true;
	}

	/**
	 * Load the session data from the handler.
	 *
	 * @return void
	 */
	protected function loadSession()
	{
		$this->attributes = $this->readFromHandler();

		foreach (array_merge($this->bags, array($this->metaBag)) as $bag)
		{
			$this->initializeLocalBag($bag);

			$bag->initialize($this->bagData[$bag->getStorageKey()]);
		}
	}

	/**
	 * Read the session data from the handler.
	 *
	 * @return array
	 */
	protected function readFromHandler()
	{
		$data = $this->handler->read($this->getId());

		return $data ? unserialize($data) : array();
	}

	/**
	 * Initialize a bag in storage if it doesn't exist.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Session\SessionBagInterface  $bag
	 * @return void
	 */
	protected function initializeLocalBag($bag)
	{
		$this->bagData[$bag->getStorageKey()] = $this->get($bag->getStorageKey(), array());

		$this->forget($bag->getStorageKey());
	}

	/**
	 * {@inheritdoc}
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setId($id)
	{
		$this->id = $id ?: $this->generateSessionId();
	}

	/**
	 * Get a new, random session ID.
	 *
	 * @return string
	 */
	protected function generateSessionId()
	{
		return sha1(uniqid(true).str_random(25).microtime(true));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * {@inheritdoc}
	 */
	public function invalidate($lifetime = null)
	{
		$this->attributes = array();

		$this->migrate();

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function migrate($destroy = false, $lifetime = null)
	{
		if ($destroy) $this->handler->destroy($this->getId());

		$this->id = $this->generateSessionId(); return true;
	}

	/**
	 * Generate a new session identifier.
	 *
	 * @param  bool  $destroy
	 * @return bool
	 */
	public function regenerate($destroy = false)
	{
		return $this->migrate($destroy);
	}

	/**
	 * {@inheritdoc}
	 */
	public function save()
	{
		$this->addBagDataToSession();

		$this->ageFlashData();

		$this->handler->write($this->getId(), serialize($this->attributes));

		$this->started = false;
	}

	/**
	 * Merge all of the bag data into the session.
	 *
	 * @return void
	 */
	protected function addBagDataToSession()
	{
		foreach (array_merge($this->bags, array($this->metaBag)) as $bag)
		{
			$this->put($bag->getStorageKey(), $this->bagData[$bag->getStorageKey()]);
		}
	}

	/**
	 * Age the flash data for the session.
	 *
	 * @return void
	 */
	public function ageFlashData()
	{
		foreach ($this->get('flash.old', array()) as $old) { $this->forget($old); }

		$this->put('flash.old', $this->get('flash.new', array()));

		$this->put('flash.new', array());
	}

	/**
	 * {@inheritdoc}
	 */
	public function has($name)
	{
		return ! is_null($this->get($name));
	}

	/**
	 * {@inheritdoc}
	 */
	public function get($name, $default = null)
	{
		return array_get($this->attributes, $name, $default);
	}

	/**
	 * Determine if the session contains old input.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function hasOldInput($key = null)
	{
		$old = $this->getOldInput($key);

		return is_null($key) ? count($old) > 0 : ! is_null($old);
	}

	/**
	 * Get the requested item from the flashed input array.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public function getOldInput($key = null, $default = null)
	{
		$input = $this->get('_old_input', array());

		// Input that is flashed to the session can be easily retrieved by the
		// developer, making repopulating old forms and the like much more
		// convenient, since the request's previous input is available.
		if (is_null($key)) return $input;

		return array_get($input, $key, $default);
	}

	/**
	 * {@inheritdoc}
	 */
	public function set($name, $value)
	{
		array_set($this->attributes, $name, $value);
	}

	/**
	 * Put a key / value pair or array of key / value pairs in the session.
	 *
	 * @param  string|array  $key
	 * @param  mixed|null  	 $value
	 * @return void
	 */
	public function put($key, $value)
	{
		if ( ! is_array($key)) $key = array($key => $value);

		foreach ($key as $arrayKey => $arrayValue)
		{
			$this->set($arrayKey, $arrayValue);
		}
	}

	/**
	 * Push a value onto a session array.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function push($key, $value)
	{
		$array = $this->get($key, array());

		$array[] = $value;

		$this->put($key, $array);
	}

	/**
	 * Flash a key / value pair to the session.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function flash($key, $value)
	{
		$this->put($key, $value);

		$this->push('flash.new', $key);

		$this->removeFromOldFlashData(array($key));
	}

	/**
	 * Flash an input array to the session.
	 *
	 * @param  array  $value
	 * @return void
	 */
	public function flashInput(array $value)
	{
		$this->flash('_old_input', $value);
	}

	/**
	 * Reflash all of the session flash data.
	 *
	 * @return void
	 */
	public function reflash()
	{
		$this->mergeNewFlashes($this->get('flash.old', array()));

		$this->put('flash.old', array());
	}

	/**
	 * Reflash a subset of the current flash data.
	 *
	 * @param  array|dynamic  $keys
	 * @return void
	 */
	public function keep($keys = null)
	{
		$keys = is_array($keys) ? $keys : func_get_args();

		$this->mergeNewFlashes($keys);

		$this->removeFromOldFlashData($keys);
	}

	/**
	 * Merge new flash keys into the new flash array.
	 *
	 * @param  array  $keys
	 * @return void
	 */
	protected function mergeNewFlashes(array $keys)
	{
		$values = array_unique(array_merge($this->get('flash.new', array()), $keys));

		$this->put('flash.new', $values);
	}

	/**
	 * Remove the given keys from the old flash data.
	 *
	 * @param  array  $keys
	 * @return void
	 */
	protected function removeFromOldFlashData(array $keys)
	{
		$this->put('flash.old', array_diff($this->get('flash.old', array()), $keys));
	}

	/**
	 * {@inheritdoc}
	 */
	public function all()
	{
		return $this->attributes;
	}

	/**
	 * {@inheritdoc}
	 */
	public function replace(array $attributes)
	{
		foreach ($attributes as $key => $value)
		{
			$this->put($key, $value);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function remove($name)
	{
		return array_pull($this->attributes, $name);
	}

	/**
	 * Remove an item from the session.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function forget($key)
	{
		array_forget($this->attributes, $key);
	}

	/**
	 * {@inheritdoc}
	 */
	public function clear()
	{
		$this->attributes = array();

		foreach ($this->bags as $bag)
		{
			$bag->clear();
		}
	}

	/**
	 * Remove all of the items from the session.
	 *
	 * @return void
	 */
	public function flush()
	{
		$this->clear();
	}

	/**
	 * {@inheritdoc}
	 */
	public function isStarted()
	{
		return $this->started;
	}

	/**
	 * {@inheritdoc}
	 */
	public function registerBag(SessionBagInterface $bag)
	{
		$this->bags[$bag->getStorageKey()] = $bag;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBag($name)
	{
		return array_get($this->bags, $name, function()
		{
			throw new \InvalidArgumentException("Bag not registered.");
		});
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMetadataBag()
	{
		return $this->metaBag;
	}

	/**
	 * Get the raw bag data array for a given bag.
	 *
	 * @param  string  $name
	 * @return array
	 */
	public function getBagData($name)
	{
		return array_get($this->bagData, $name, array());
	}

	/**
	 * Get the CSRF token value.
	 *
	 * @return string
	 */
	public function token()
	{
		return $this->get('_token');
	}

	/**
	 * Get the CSRF token value.
	 *
	 * @return string
	 */
	public function getToken()
	{
		return $this->token();
	}

	/**
	 * Regenerate the CSRF token value.
	 *
	 * @return void
	 */
	public function regenerateToken()
	{
		$this->put('_token', str_random(40));
	}

	/**
	 * Get the underlying session handler implementation.
	 *
	 * @return \SessionHandlerInterface
	 */
	public function getHandler()
	{
		return $this->handler;
	}

	/**
	 * Determine if the session handler needs a request.
	 *
	 * @return bool
	 */
	public function handlerNeedsRequest()
	{
		return $this->handler instanceof CookieSessionHandler;
	}

	/**
	 * Set the request on the handler instance.
	 *
	 * @param  \Symfony\Component\HttpFoundation\Request  $request
	 * @return void
	 */
	public function setRequestOnHandler(Request $request)
	{
		if ($this->handlerNeedsRequest())
		{
			$this->handler->setRequest($request);
		}
	}

}
