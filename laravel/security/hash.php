<?php namespace Laravel\Security; use Laravel\Str;

class Hash {

	/**
	 * Hash a password using the Bcrypt hashing scheme.
	 *
	 * Bcrypt provides a future-proof hashing algorithm by allowing the
	 * number of "rounds" to be increased, thus increasing the time it
	 * takes to generate the hashed value. The longer it takes takes
	 * to generate the hash, the more impractical a rainbow table
	 * attack against the hashes becomes.
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
		return crypt($value, '$2a$'.str_pad($rounds, 2, '0', STR_PAD_LEFT).'$'.static::salt());
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
		// Bcrypt expects the salt to be 22 base64 encoded characters, including dots
		// and slashes. We will get rid of the plus signs included in the base64 data
		// and replace them with dots. OpenSSL will be used if available, since it is
		// more random, otherwise we will fallback on Str::random.
		if (function_exists('openssl_random_pseudo_bytes'))
		{
			$bytes = openssl_random_pseudo_bytes(16);

			return substr(strtr(base64_encode($bytes), '+', '.'), 0 , 22);
		}

		return substr(str_replace('+', '.', base64_encode(Str::random(40))), 0, 22);
	}

}