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
			return mb_strtolower($value, Config::get('application.encoding'));
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
			return mb_strtoupper($value, Config::get('application.encoding'));
		}

		return strtoupper($value);
	}

	/**
	 * Convert a string to title case (ucwords equivalent).
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
	 * <code>
	 *		// Returns "Tay..."
	 *		echo Str::limit('Taylor Otwell', 3);
	 *
	 *		// Limit the number of characters and append a custom ending
	 *		echo Str::limit('Taylor Otwell', 3, '---');
	 * </code>
	 *
	 * @param  string  $value
	 * @param  int     $limit
	 * @param  string  $end
	 * @return string
	 */
	public static function limit($value, $limit = 100, $end = '...')
	{
		if (static::length($value) <= $limit) return $value;

		if (function_exists('mb_substr'))
		{
			return mb_substr($value, 0, $limit, Config::get('application.encoding')).$end;
		}

		return substr($value, 0, $limit).$end;
	}

	/**
	 * Limit the number of words in a string. Word integrity is preserved.
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
		if (trim($value) == '') return $value;

		preg_match('/^\s*+(?:\S++\s*+){1,'.$words.'}/', $value, $matches);

		if (static::length($value) == static::length($matches[0]))
		{
			$end = '';
		}

		return rtrim($matches[0]).$end;
	}

	/**
	 * Convert a string to 7-bit ASCII.
	 *
	 * This is helpful for converting UTF-8 strings for usage in URLs, etc.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function ascii($value)
	{
		$foreign = Config::get('strings.ascii');

		$value = preg_replace(array_keys($foreign), array_values($foreign), $value);

		return preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $value);
	}

	/**
	 * Get the plural form of the given word.
	 *
	 * The word must be defined in the "strings" configuration file.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function plural($value, $count = null)
	{
		if ( ! is_null($count) and $count == 1) return $value;

		return array_get(Config::get('strings.inflection'), $value, $value);
	}

	/**
	 * Get the singular form of the given word.
	 *
	 * The word must be defined in the "strings" configuration file.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function singular($value)
	{
		return array_get(array_flip(Config::get('strings.inflection')), $value, $value);
	}

	/**
	 * Generate a random alpha or alpha-numeric string.
	 *
	 * <code>
	 *		// Generate a 40 character random alpha-numeric string
	 *		echo Str::random(40);
	 *
	 *		// Generate a 16 character random alphabetic string
	 *		echo Str::random(16, 'alpha');
	 * <code>
	 *
	 * @param  int	   $length
	 * @param  string  $type
	 * @return string
	 */
	public static function random($length, $type = 'alnum')
	{
		return substr(str_shuffle(str_repeat(static::pool($type), 5)), 0, $length);
	}

	/**
	 * Get the character pool for a given type of random string.
	 *
	 * @param  string  $type
	 * @return string
	 */
	protected static function pool($type)
	{
		switch ($type)
		{
			case 'alpha':
				return 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

			case 'alnum':
				return '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

			default:
				throw new \Exception("Invalid random string type [$type].");
		}
	}

}
