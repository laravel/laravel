<?php namespace LCQRS\Message\Drivers;

use Laravel\Redis;
use Closure;

class Redis extends Driver {

	/**
	 * The Redis database instance.
	 *
	 * @var Laravel\Redis
	 */
	protected $redis;

	/**
	 * The channel subscriptions
	 *
	 * @var mixed
	 */
	public $subscriptions = array();

	/**
	 * Create a new Redis message driver instance.
	 *
	 * @param  Laravel\Redis  $redis
	 * @return void
	 */
	public function __construct(Redis $redis)
	{
		$this->redis = $redis;
	}

	/**
	 * Publish a message to a channel
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function pub($channel, $message)
	{
		$this->redis->publish($channel, $message);
	}

	/**
	 * Add subsciption for channel
	 *
	 * @param  string   $channel
	 * @param  closure  $callback
	 * @return void
	 */
	public function sub($channel, Closure $callback)
	{
		$this->subscriptions[$channel][] = $callback;
	}

}