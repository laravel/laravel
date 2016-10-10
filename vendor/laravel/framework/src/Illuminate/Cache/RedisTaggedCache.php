<?php namespace Illuminate\Cache;

class RedisTaggedCache extends TaggedCache {

	/**
	 * Store an item in the cache indefinitely.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function forever($key, $value)
	{
		$this->pushForeverKeys($namespace = $this->tags->getNamespace(), $key);

		$this->store->forever($this->getPrefix().sha1($namespace).':'.$key, $value);
	}

	/**
	 * Remove all items from the cache.
	 *
	 * @return void
	 */
	public function flush()
	{
		$this->deleteForeverKeys();

		parent::flush();
	}

	/**
	 * Store a copy of the full key for each namespace segment.
	 *
	 * @param  string  $namespace
	 * @param  string  $key
	 * @return void
	 */
	protected function pushForeverKeys($namespace, $key)
	{
		$fullKey = $this->getPrefix().sha1($namespace).':'.$key;

		foreach (explode('|', $namespace) as $segment)
		{
			$this->store->connection()->lpush($this->foreverKey($segment), $fullKey);
		}
	}

	/**
	 * Delete all of the items that were stored forever.
	 *
	 * @return void
	 */
	protected function deleteForeverKeys()
	{
		foreach (explode('|', $this->tags->getNamespace()) as $segment)
		{
			$this->deleteForeverValues($segment = $this->foreverKey($segment));

			$this->store->connection()->del($segment);
		}
	}

	/**
	 * Delete all of the keys that have been stored forever.
	 *
	 * @param  string  $foreverKey
	 * @return void
	 */
	protected function deleteForeverValues($foreverKey)
	{
		$forever = array_unique($this->store->connection()->lrange($foreverKey, 0, -1));

		if (count($forever) > 0)
		{
			call_user_func_array(array($this->store->connection(), 'del'), $forever);
		}
	}

	/**
	 * Get the forever reference key for the segment.
	 *
	 * @param  string  $segment
	 * @return string
	 */
	protected function foreverKey($segment)
	{
		return $this->getPrefix().$segment.':forever';
	}

}
