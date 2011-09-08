<?php namespace Laravel;

class Str {

	/**
	 * Convert a string to lowercase.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function lower($value)
	{
		if (function_exists('mb_strtolower'))
		{
			return mb_strtolower($value, static::encoding());
		}

		return strtolower($value);
	}

	/**
	 * Convert a string to uppercase.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function upper($value)
	{
		if (function_exists('mb_strtoupper'))
		{
			return mb_strtoupper($value, static::encoding());
		}

		return strtoupper($value);
	}

	/**
	 * Convert a string to title case (ucwords).
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function title($value)
	{
		if (function_exists('mb_convert_case'))
		{
			return mb_convert_case($value, MB_CASE_TITLE, static::encoding());
		}

		return ucwords(strtolower($value));
	}

	/**
	 * Get the length of a string.
	 *
	 * @param  string  $value
	 * @return int
	 */
	public static function length($value)
	{
		if (function_exists('mb_strlen'))
		{
			return mb_strlen($value, static::encoding());
		}

		return strlen($value);
	}

	/**
	 * Convert a string to 7-bit ASCII.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function ascii($value)
	{
		$foreign = IoC::container()->resolve('laravel.config')->get('ascii');

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
		$alpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		$pool = ($type == 'alpha_num') ? '0123456789'.$alpha : $alpha;

		return implode('', array_map(function() use ($pool) { return $pool[mt_rand(0, strlen($pool) - 1)]; }, range(0, $length)));
	}

	/**
	 * Get the application encoding from the configuration class.
	 *
	 * @return string
	 */
	protected static function encoding()
	{
		return IoC::container()->resolve('laravel.config')->get('application.encoding');
	}

}