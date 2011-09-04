<?php namespace Laravel\Security\Hashing;

use Laravel\Facade;

class Hasher_Facade extends Facade {

	public static $resolve = 'hasher';

}

class Hasher {

	/**
	 * The hashing engine being used to perform the hashing.
	 *
	 * @var Hash\Engine
	 */
	public $engine;

	/**
	 * Create a new Hasher instance.
	 *
	 * If no hashing engine is provided, the BCrypt engine will be used.
	 *
	 * @param  Engine  $engine
	 * @return void
	 */
	public function __construct(Engine $engine = null)
	{
		$this->engine = (is_null($engine)) ? new BCrypt(10, false) : $engine;
	}

	/**
	 * Magic Method for delegating method calls to the hashing engine.
	 *
	 * <code>
	 *		// Use the hashing engine to has a value
	 *		$hash = Hasher::make()->hash('password');
	 *
	 *		// Equivalent method using the engine property
	 *		$hash = Hasher::make()->engine->hash('password');
	 * </code>
	 */
	public function __call($method, $parameters)
	{
		return call_user_func_array(array($this->engine, $method), $parameters);
	}

	/**
	 * Magic Method for performing methods on the default hashing engine.
	 *
	 * <code>
	 *		// Hash a value using the default hashing engine
	 *		$hash = Hasher::hash('password');
	 * </code>
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::make()->engine, $method), $parameters);
	}

}