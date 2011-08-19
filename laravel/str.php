<?php namespace Laravel;

class Str {

	/**
	 * Convert HTML characters to entities.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function entities($value)
	{
		return htmlentities($value, ENT_QUOTES, Config::get('application.encoding'), false);
	}

	/**
	 * Convert a string to lowercase.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function lower($value)
	{
		return function_exists('mb_strtolower') ? mb_strtolower($value, Config::get('application.encoding')) : strtolower($value);
	}

	/**
	 * Convert a string to uppercase.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function upper($value)
	{
		return function_exists('mb_strtoupper') ? mb_strtoupper($value, Config::get('application.encoding')) : strtoupper($value);
	}

	/**
	 * Convert a string to title case (ucwords).
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function title($value)
	{
		return (function_exists('mb_convert_case')) ? mb_convert_case($value, MB_CASE_TITLE, Config::get('application.encoding')) : ucwords(strtolower($value));
	}

	/**
	 * Get the length of a string.
	 *
	 * @param  string  $value
	 * @return int
	 */
	public static function length($value)
	{
		return function_exists('mb_strlen') ? mb_strlen($value, Config::get('application.encoding')) : strlen($value);
	}

	/**
	 * Convert a string to 7-bit ASCII.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function ascii($value)
	{
		$foreign = Config::get('ascii');

		$value = preg_replace(array_keys($foreign), array_values($foreign), $value);

		return preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $value);
	}

	/**
	 * Generate a random alpha or alpha-numeric string.
	 *
	 * Supported types: 'alpha_num' and 'alpha'.
	 *
	 * @param  int	 $length
	 * @param  string  $type
	 * @return string
	 */
	public static function random($length = 16, $type = 'alpha_num')
	{
		$value = '';

		$pool_length = strlen($pool = static::pool($type)) - 1;

		for ($i = 0; $i < $length; $i++)
		{
			$value .= $pool[mt_rand(0, $pool_length)];
		}

		return $value;
	}

	/**
	 * Get a chracter pool.
	 *
	 * @param  string  $type
	 * @return string
	 */
	private static function pool($type = 'alpha_num')
	{
		switch ($type)
		{
			case 'alpha_num':
				return '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			
			default:
				return 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		}
	}

}