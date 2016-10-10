<?php namespace Illuminate\Cache;

class TagSet {

	/**
	 * The cache store implementation.
	 *
	 * @var \Illuminate\Cache\StoreInterface
	 */
	protected $store;

	/**
	 * The tag names.
	 *
	 * @var array
	 */
	protected $names = array();

	/**
	 * Create a new TagSet instance.
	 *
	 * @param  \Illuminate\Cache\StoreInterface  $store
	 * @param  array  $names
	 * @return void
	 */
	public function __construct(StoreInterface $store, array $names = array())
	{
		$this->store = $store;
		$this->names = $names;
	}

	/**
	 * Reset all tags in the set.
	 *
	 * @return void
	 */
	public function reset()
	{
		array_walk($this->names, array($this, 'resetTag'));
	}

	/**
	 * Get the unique tag identifier for a given tag.
	 *
	 * @param  string  $name
	 * @return string
	 */
	public function tagId($name)
	{
		return $this->store->get($this->tagKey($name)) ?: $this->resetTag($name);
	}

	/**
	 * Get an array of tag identifiers for all of the tags in the set.
	 *
	 * @return array
	 */
	protected function tagIds()
	{
		return array_map(array($this, 'tagId'), $this->names);
	}

	/**
	 * Get a unique namespace that changes when any of the tags are flushed.
	 *
	 * @return string
	 */
	public function getNamespace()
	{
		return implode('|', $this->tagIds());
	}

	/**
	 * Reset the tag and return the new tag identifier
	 *
	 * @param  string  $name
	 * @return string
	 */
	public function resetTag($name)
	{
		$this->store->forever($this->tagKey($name), $id = str_replace('.', '', uniqid('', true)));

		return $id;
	}

	/**
	 * Get the tag identifier key for a given tag.
	 *
	 * @param  string  $name
	 * @return string
	 */
	public function tagKey($name)
	{
		return 'tag:'.$name.':key';
	}

}
