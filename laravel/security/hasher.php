<?php namespace Laravel\Security; use Laravel\Str;

class Hasher {

	/**
	 * Hash a password using the Bcrypt hashing scheme.
	 *
	 * Bcrypt provides a future-proof hashing algorithm by allowing the number of "rounds"
	 * to be increased, thus increasing the time is takes to generate the hashed value.
	 * The longer is takes to generate the hash, the more impractical a rainbow table
	 * attack against the hashes becomes.
	 *
	 * <code>
	 *		// Create a Bcrypt hash of a value
	 *		$hash = Hasher::hash('secret');
	 *
	 *		// Use a specified number of iterations when creating the hash
	 *		$hash = Hasher::hash('secret', 12);
	 * </code>
	 *
	 * @param  string  $value
	 * @param  int     $rounds
	 * @return string
	 */
	public static function hash($value, $rounds = 8)
	{
		return crypt($value, '$2a$'.str_pad($rounds, 2, '0', STR_PAD_LEFT).'$'.static::salt());
	}

	/**
	 * Determine if an unhashed value matches a given Bcrypt hash.
	 *
	 * Since the number of rounds is included in the Bcrypt hash, it is not
	 * necessary to specify the rounds when calling this method.
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
		// If OpenSSL is installed, we will use it to gather random bytes for generating
		// the salt value. Otherwise, we will use the Str::random method. Bcrypt expects
		// the salt to be a 22 character alpha-numeric string. The salt may also contain
		// dots, plus signs, and forward slashes.
		if (function_exists('openssl_random_pseudo_bytes'))
		{
			return substr(strtr(base64_encode(openssl_random_pseudo_bytes(16)), '+', '.'), 0 , 22);
		}

		return substr(str_replace('+', '.', base64_encode(Str::random(40))), 0, 22);
	}

}