<?php namespace Laravel; defined('APP_PATH') or die('No direct script access.');

if ( ! function_exists('mcrypt_encrypt'))
{
	throw new \Exception('Mcrypt must be installed before using the Crypter class.');
}

if (trim(Config::get('application.key')) === '')
{
	throw new \Exception('The Crypter class may not be used without an application key.');
}

class Crypter {

	/**
	 * The encryption cipher.
	 *
	 * @var string
	 */
	public static $cipher = MCRYPT_RIJNDAEL_256;

	/**
	 * The encryption mode.
	 *
	 * @var string
	 */
	public static $mode = MCRYPT_MODE_CBC;

	/**
	 * Encrypt a string using Mcrypt.
	 *
	 * The string will be encrypted using the AES-256 scheme and will be base64 encoded.
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
	 * @param  string  $value
	 * @return string
	 */
	public static function decrypt($value)
	{
		if (($value = base64_decode($value)) === false)
		{
			throw new \Exception('Input value is not valid base64 data.');
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
		return Config::get('application.key');
	}

}