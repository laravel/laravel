<?php namespace System;

class Crypt {

	/**
	 * The encryption cipher.
	 *
	 * @var string
	 */
	public static $cipher = 'rijndael-256';

	/**
	 * The encryption mode.
	 *
	 * @var string
	 */
	public static $mode = 'cbc';

	/**
	 * Encrypt a value using the MCrypt library.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function encrypt($value)
	{
		// -----------------------------------------------------
		// Determine the input vector source. Different servers
		// and operating systems will have varying options.
		// -----------------------------------------------------
		if (defined('MCRYPT_DEV_URANDOM'))
		{
			$random = MCRYPT_DEV_URANDOM;
		}
		elseif (defined('MCRYPT_DEV_RANDOM'))
		{
			$random = MCRYPT_DEV_RANDOM;
		}
		else
		{
			$random = MCRYPT_RAND;
		}

		// -----------------------------------------------------
		// The system random number generator must be seeded
		// to produce adequately random results.
		// -----------------------------------------------------
		if ($random === MCRYPT_RAND)
		{
			mt_srand();
		}

		$iv = mcrypt_create_iv(static::iv_size(), $random);
		$value = mcrypt_encrypt(static::$cipher, static::key(), $value, static::$mode, $iv);

		// -----------------------------------------------------
		// We use base64 encoding to get a nice string value.
		// -----------------------------------------------------
		return base64_encode($iv.$value);
	}

	/**
	 * Decrypt a value using the MCrypt library.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function decrypt($value)
	{
		// -----------------------------------------------------
		// Since all of our encrypted values are base64 encoded,
		// we will decode the value here and verify it.
		// -----------------------------------------------------
		$value = base64_decode($value, true);

		if ( ! $value)
		{
			throw new \Exception('Decryption error. Input value is not valid base64 data.');
		}

		// -----------------------------------------------------
		// Extract the input vector from the value.
		// -----------------------------------------------------
		$iv = substr($value, 0, static::iv_size());

		// -----------------------------------------------------
		// Remove the input vector from the encrypted value.
		// -----------------------------------------------------
		$value = substr($value, static::iv_size());

		return rtrim(mcrypt_decrypt(static::$cipher, static::key(), $value, static::$mode, $iv), "\0");
	}

	/**
	 * Get the application key.
	 *
	 * @return string
	 */
	private static function key()
	{
		if (is_null($key = Config::get('application.key')) or $key == '')
		{
			throw new \Exception("The encryption class can not be used without an encryption key.");
		}

		return $key;
	}

	/**
	 * Get the input vector size for the cipher and mode.
	 *
	 * Different ciphers and modes use varying lengths of input vectors.
	 *
	 * @return int
	 */
	private static function iv_size()
	{
		return mcrypt_get_iv_size(static::$cipher, static::$mode);
	}

}