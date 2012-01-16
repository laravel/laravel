<?php namespace Laravel;

class Hash {

	/**
	 * Hash a password using the Bcrypt hashing scheme.
	 *
	 * <code>
	 *		// Create a Bcrypt hash of a value
	 *		$hash = Hash::make('secret');
	 *
	 *		// Use a specified number of iterations when creating the hash
	 *		$hash = Hash::make('secret', 12);
	 * </code>
	 *
	 * @param  string  $value
	 * @param  int     $rounds
	 * @return string
	 */
	public static function make($value, $rounds = 8)
	{
		$work = str_pad($rounds, 2, '0', STR_PAD_LEFT);

		return crypt($value, '$2a$'.$work.'$'.static::salt());
	}

	/**
	 * Determine if an unhashed value matches a given Bcrypt hash.
	 *
	 * @param  string  $value
	 * @param  string  $hash
	 * @return bool
	 */
	public static function check($value, $hash)
	{
		return crypt($value, $hash) === $hash;
	}

	/**
	 * Get a salt for use during Bcrypt hashing.
	 *
	 * @return string
	 */
	protected static function salt()
	{
		// Bcrypt expects the salt to be 22 base64 encoded characters including
		// dots and slashes. We will get rid of the plus signs included in the
		// base64 data and replace them with dots. OpenSSL will be used if it
		// is available, otherwise we will use the Str::random method.
		if (function_exists('openssl_random_pseudo_bytes'))
		{
			$bytes = openssl_random_pseudo_bytes(16);

			return substr(strtr(base64_encode($bytes), '+', '.'), 0 , 22);
		}

		$salt = str_replace('+', '.', base64_encode(Str::random(40)));

		return substr($salt, 0, 22);
	}

}