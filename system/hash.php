<?php namespace System;

class Hash {

    /**
	 * The salty, hashed value.
	 *
	 * @var string
	 */
	public $value;

	/**
	 * The salt used during hashing.
	 *
	 * @var string
	 */
	public $salt;

	/**
	 * Create a new salted hash instance.
	 *
	 * If no salt is provided, a random, 16 character salt will be generated
	 * to created the salted, hashed value. If a salt is provided, that salt
	 * will be used when hashing the value.
	 *
	 * @param  string  $value
	 * @param  string  $salt
	 * @return void
	 */
	public function __construct($value, $salt = null)
	{
		$this->salt = (is_null($salt)) ? Str::random(16) : $salt;

		$this->value = sha1($value.$this->salt);
	}

	/**
	 * Factory for creating hash instances.
	 *
	 * @access public
	 * @param  string  $value
	 * @param  string  $salt
	 * @return Hash
	 */
	public static function make($value, $salt = null)
	{
		return new self($value, $salt);
	}

}	