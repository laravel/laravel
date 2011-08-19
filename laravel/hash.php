<?php namespace Laravel;

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
	public static function make($value, $rounds = 10)
	{
		return static::hasher($rounds)->HashPassword($value);
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
	 * @param  int  $rounds
	 * @return PasswordHash
	 */
	private static function hasher($rounds = 10)
	{
		require_once SYS_PATH.'vendor/phpass'.EXT;

		return new \PasswordHash($rounds, false);
	}

}