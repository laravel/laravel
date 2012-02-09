<?php namespace Laravel; defined('DS') or die('No direct script access.');

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
		$value = base64_decode($value);

		// To decrypt the value, we first need to extract the input vector and
		// the encrypted value. The input vector size varies across different
		// encryption ciphers and modes, so we'll get the correct size.
		$iv = substr($value, 0, static::iv_size());

		$value = substr($value, static::iv_size());

		// Once we have the input vector and the value, we can give them both
		// to Mcrypt for decryption. The value is sometimes padded with \0,
		// so we will trim all of the padding characters.
		$key = static::key();

		return rtrim(mcrypt_decrypt(static::$cipher, $key, $value, static::$mode, $iv), "\0");
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