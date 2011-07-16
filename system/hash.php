<?php namespace System;

class Hash {

	/**
	 * Hash a string using PHPass.
	 *
	 * PHPass provides reliable bcrypt hashing, and is used by many popular PHP
	 * applications such as Wordpress and Joomla.
	 *
	 * @access public
	 * @param  string  $value
	 * @return string
	 */
	public static function make($value)
	{
		return static::hasher()->HashPassword($value);
	}

	/**
	 * Determine if an unhashed value matches a given hash.
	 *
	 * @param  string  $value
	 * @param  string  $hash
	 * @return bool
	 */
	public static function check($value, $hash)
	{
		return static::hasher()->CheckPassword($value, $hash);
	}

	/**
	 * Create a new PHPass instance.
	 *
	 * @return PasswordHash
	 */
	private static function hasher()
	{
		require_once SYS_PATH.'vendor/phpass'.EXT;

		return new \PasswordHash(10, false);
	}

}	