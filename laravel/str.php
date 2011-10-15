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
		if (function_exists('mb_strtolower'))
		{
			return mb_strtolower($value, Config::get('application.encoding'));
		}

		return strtolower($value);
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
		if (function_exists('mb_strtoupper'))
		{
			return mb_strtoupper($value, Config::get('application.encoding'));
		}

		return strtoupper($value);
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
		if (function_exists('mb_convert_case'))
		{
			return mb_convert_case($value, MB_CASE_TITLE, Config::get('application.encoding'));
		}

		return ucwords(strtolower($value));
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
		if (function_exists('mb_strlen'))
		{
			return mb_strlen($value, Config::get('application.encoding'));
		}

		return strlen($value);
	}

	/**
	 * Limit the number of characters in a string.
	 *
	 * Word integrity is preserved, so the number of characters in the
	 * truncated string will be rounded to the nearest word ending.
	 *
	 * <code>
	 *		// Returns "Taylor..."
	 *		echo Str::limit('Taylor Otwell', 3);
	 *
	 *		// Limit the number of characters and append a custom ending
	 *		echo Str::limit('Taylor Otwell', 3, '---');
	 * </code>
	 *
	 * @param  string  $value
	 * @param  int     $length
	 * @param  string  $end
	 * @return string
	 */
	public static function limit($value, $limit = 100, $end = '...')
	{
		if (static::length($value) < $limit) return $value;

		$limit = preg_replace('/\s+?(\S+)?$/', '', substr($value, 0, $limit));

		return (static::length($limit) == static::length($value)) ? $value : $limit.$end;
	}

	/**
	 * Limit the number of words in a string
	 *
	 * <code>
	 *		// Returns "This is a..."
	 *		echo Str::words('This is a sentence.', 3);
	 *
	 *		// Limit the number of words and append a custom ending
	 *		echo Str::words('This is a sentence.', 3, '---');
	 * </code>
	 *
	 * @param  string  $value
	 * @param  int     $length
	 * @param  string  $end
	 * @return string
	 */
	public static function words($value, $words = 100, $end = '...')
	{
		$count = str_word_count($value, 1);

		if ($count <= $words) return $value;

		return implode(' ', array_slice($count, 0, $words)).$end;
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
		$foreign = Config::get('ascii');

		$value = preg_replace(array_keys($foreign), array_values($foreign), $value);

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