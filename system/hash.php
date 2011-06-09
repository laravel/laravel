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
	 * Create a new hash instance.
	 *
	 * @param  string  $value
	 * @param  string  $salt
	 * @return void
	 */
	public function __construct($value, $salt = null)
	{
		// --------------------------------------------------------------
		// Get a random salt to hash the value with.
		// --------------------------------------------------------------
		$this->salt = (is_null($salt)) ? Str::random(16) : $salt;

		// --------------------------------------------------------------
		// Perform a salted, SHA-1 hash on the value.
		// --------------------------------------------------------------
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