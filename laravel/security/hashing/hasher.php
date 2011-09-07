<?php namespace Laravel\Security\Hashing;

class Hasher {

	/**
	 * The hashing engine being used to perform the hashing.
	 *
	 * @var Hash\Engine
	 */
	protected $engine;

	/**
	 * Create a new Hasher instance.
	 *
	 * @param  Engine  $engine
	 * @return void
	 */
	public function __construct(Engine $engine)
	{
		$this->engine = $engine
	}

	/**
	 * Magic Method for delegating method calls to the hashing engine.
	 */
	public function __call($method, $parameters)
	{
		return call_user_func_array(array($this->engine, $method), $parameters);
	}

	/**
	 * Magic Method for performing methods on the default hashing engine.
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::make()->engine, $method), $parameters);
	}

}