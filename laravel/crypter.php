<?php namespace Laravel;

if (trim(Config::$items['application']['key']) === '')
{
	throw new \LogicException('The encryption class may not be used without an application key.');
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
	protected static $mode = MCRYPT_MODE_CBC;

	/**
	 * Encrypt a string using Mcrypt.
	 *
	 * The given string will be encrypted using AES-256 encryption for a high
	 * degree of security. The returned string will also be base64 encoded.
	 *
	 * Mcrypt must be installed on your machine before using this method, and
	 * an application key must be specified in the application configuration.
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
		$iv = mcrypt_create_iv(static::iv_size(), MCRYPT_RAND);

		$value = mcrypt_encrypt(static::$cipher, static::key(), $value, static::$mode, $iv);

		return base64_encode($iv.$value);
	}

	/**
	 * Decrypt a string using Mcrypt.
	 *
	 * The given encrypted value must have been encrypted using Laravel and
	 * the application key specified in the application configuration file.
	 *
	 * Mcrypt must be installed on your machine before using this method.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function decrypt($value)
	{
		if (($value = base64_decode($value)) === false)
		{
			throw new \InvalidArgumentException('Input value is not valid base64 data.');
		}

		$iv = substr($value, 0, static::iv_size());

		$value = substr($value, static::iv_size());

		return rtrim(mcrypt_decrypt(static::$cipher, static::key(), $value, static::$mode, $iv), "\0");
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