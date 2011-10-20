<?php namespace Laravel\Security; use Laravel\Config;

if (trim(Config::$items['application']['key']) === '')
{
	throw new \Exception('The encryption class may not be used without an application key.');
}

class Crypter {

	/**
	 * The encryption cipher.
	 *
	 * @var string
	 */
	protected static $cipher = MCRYPT_RIJNDAEL_256;

	/**
	 * The encryption mode.
	 *
	 * @var string
	 */
	protected static $mode = 'cbc';

	/**
	 * Encrypt a string using Mcrypt.
	 *
	 * The string will be encrypted using the cipher and mode specified when the
	 * crypter instance was created, and the final result will be base64 encoded.
	 *
	 * <code>
	 *		// Encrypt a string using the Mcrypt PHP extension
	 *		$encrypted = Crypter::encrpt('secret');
	 * </code>
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function encrypt($value)
	{
		// Determine the most appropriate random number generator for the
		// OS and system and environment the application is running on.
		if (defined('MCRYPT_DEV_URANDOM'))
		{
			$randomizer = MCRYPT_DEV_URANDOM;
		}
		elseif (defined('MCRYPT_DEV_RANDOM'))
		{
			$randomizer = MCRYPT_DEV_RANDOM;
		}
		else
		{
			$randomizer = MCRYPT_RAND;			
		}

		$iv = mcrypt_create_iv(static::iv_size(), $randomizer);

		return base64_encode($iv.mcrypt_encrypt(static::$cipher, static::key(), $value, static::$mode, $iv));
	}

	/**
	 * Decrypt a string using Mcrypt.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function decrypt($value)
	{
		list($iv, $value) = static::parse(base64_decode($value, true));

		return rtrim(mcrypt_decrypt(static::$cipher, static::key(), $value, static::$mode, $iv), "\0");
	}

	/**
	 * Parse an encrypted value into the input vector and the actual value.
	 *
	 * If the given value is not valid base64 data, an exception will be thrown.
	 *
	 * @param  string  $value
	 * @return array
	 */
	protected static function parse($value)
	{
		if ( ! is_string($value))
		{
			throw new \Exception('Decryption error. Input value is not valid base64 data.');
		}

		return array(substr($value, 0, static::iv_size()), substr($value, static::iv_size()));
	}

	/**
	 * Get the input vector size for the cipher and mode.
	 *
	 * @return int
	 */
	protected static function iv_size()
	{
		return mcrypt_get_iv_size(static::$cipher, static::$mode);
	}

	/**
	 * Get the encryption key from the application configuration.
	 *
	 * @return string
	 */
	protected static function key()
	{
		return Config::$items['application']['key'];
	}

}