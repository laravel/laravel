<?php namespace Laravel;

class Str {

	/**
	 * Convert a string to lowercase.
	 *
	 * <code>
	 *		// Convert a string to lowercase
	 *		echo Str::lower('STOP YELLING');
	 *
	 *		// Convert a UTF-8 string to lowercase
	 *		echo Str::lower('Τάχιστη');
	 * </code>
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function lower($value)
	{
		return (fe('mb_strtolower')) ? mb_strtolower($value, Config::get('application.encoding')) : strtolower($value);
	}

	/**
	 * Convert a string to uppercase.
	 *
	 * <code>
	 *		// Convert a string to uppercase
	 *		echo Str::upper('speak louder');
	 *
	 *		// Convert a UTF-8 string to uppercase
	 *		echo Str::upper('Τάχιστη');
	 * </code>
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function upper($value)
	{
		return (fe('mb_strtoupper')) ? mb_strtoupper($value, Config::get('application.encoding')) : strtoupper($value);
	}

	/**
	 * Convert a string to title case (ucwords equivalent).
	 *
	 * <code>
	 *		// Convert a string to title case
	 *		echo Str::title('taylor otwell');
	 *
	 *		// Convert a UTF-8 string to title case
	 *		echo Str::title('Τάχιστη αλώπηξ');
	 * </code>
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function title($value)
	{
		return (fe('mb_convert_case')) ? mb_convert_case($value, MB_CASE_TITLE, Config::get('application.encoding')) : ucwords(strtolower($value));
	}

	/**
	 * Get the length of a string.
	 *
	 * <code>
	 *		// Get the length of a string
	 *		echo Str::length('taylor otwell');
	 *
	 *		// Get the length of a UTF-8 string
	 *		echo Str::length('Τάχιστη αλώπηξ');
	 * </code>
	 *
	 * @param  string  $value
	 * @return int
	 */
	public static function length($value)
	{
		return (fe('mb_strlen')) ? mb_strlen($value, Config::get('application.encoding')) : strlen($value);
	}

	/**
	 * Convert a string to 7-bit ASCII.
	 *
	 * <code>
	 *		// Returns "Deuxieme Article"
	 *		echo Str::ascii('Deuxième Article');
	 * </code>
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function ascii($value)
	{
		$value = preg_replace(array_keys($foreign = Config::get('ascii')), array_values($foreign), $value);

		return preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $value);
	}

	/**
	 * Generate a random alpha or alpha-numeric string.
	 *
	 * <code>
	 *		// Generate a 40 character random, alpha-numeric string
	 *		echo Str::random(40);
	 *
	 *		// Generate a 16 character random, alphabetic string
	 *		echo Str::random(16, 'alpha');
	 * <code>
	 *
	 * @param  int	   $length
	 * @param  string  $type
	 * @return string
	 */
	public static function random($length, $type = 'alnum')
	{
		$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		return substr(str_shuffle(str_repeat(($type == 'alnum') ? $pool.'0123456789' : $pool, 5)), 0, $length);
	}

}