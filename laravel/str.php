<?php namespace Laravel;

class Str {

	/**
	 * The pluralizer instance.
	 *
	 * @var Pluralizer
	 */
	public static $pluralizer;

	/**
	 * Get the default string encoding for the application.
	 *
	 * This method is simply a short-cut to Config::get('application.encoding').
	 *
	 * @return string
	 */
	public static function encoding()
	{
		return Config::get('application.encoding');
	}

	/**
	 * Get the length of a string.
	 *
	 * <code>
	 *		// Get the length of a string
	 *		$length = Str::length('Taylor Otwell');
	 *
	 *		// Get the length of a multi-byte string
	 *		$length = Str::length('Τάχιστη')
	 * </code>
	 *
	 * @param  string  $value
	 * @return int
	 */
	public static function length($value)
	{
		return (MB_STRING) ? mb_strlen($value, static::encoding()) : strlen($value);
	}

	/**
	 * Convert a string to lowercase.
	 *
	 * <code>
	 *		// Convert a string to lowercase
	 *		$lower = Str::lower('Taylor Otwell');
	 *
	 *		// Convert a multi-byte string to lowercase
	 *		$lower = Str::lower('Τάχιστη');
	 * </code>
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function lower($value)
	{
		return (MB_STRING) ? mb_strtolower($value, static::encoding()) : strtolower($value);
	}

	/**
	 * Convert a string to uppercase.
	 *
	 * <code>
	 *		// Convert a string to uppercase
	 *		$upper = Str::upper('Taylor Otwell');
	 *
	 *		// Convert a multi-byte string to uppercase
	 *		$upper = Str::upper('Τάχιστη');
	 * </code>
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function upper($value)
	{
		return (MB_STRING) ? mb_strtoupper($value, static::encoding()) : strtoupper($value);
	}

	/**
	 * Convert a string to title case (ucwords equivalent).
	 *
	 * <code>
	 *		// Convert a string to title case
	 *		$title = Str::title('taylor otwell');
	 *
	 *		// Convert a multi-byte string to title case
	 *		$title = Str::title('νωθρού κυνός');
	 * </code>
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function title($value)
	{
		if (MB_STRING)
		{
			return mb_convert_case($value, MB_CASE_TITLE, static::encoding());
		}

		return ucwords(strtolower($value));
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

		if (MB_STRING)
		{
			return mb_substr($value, 0, $limit, static::encoding()).$end;
		}

		return substr($value, 0, $limit).$end;
	}

	/**
	 * Limit the number of words in a string.
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
	 * @param  int     $words
	 * @param  string  $end
	 * @return string
	 */
	public static function words($value, $words = 100, $end = '...')
	{
		if (trim($value) == '') return '';

		preg_match('/^\s*+(?:\S++\s*+){1,'.$words.'}/u', $value, $matches);

		if (static::length($value) == static::length($matches[0]))
		{
			$end = '';
		}

		return rtrim($matches[0]).$end;
	}

	/**
	 * Get the singular form of the given word.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function singular($value)
	{
		return static::pluralizer()->singular($value);
	}

	/**
	 * Get the plural form of the given word.
	 *
	 * <code>
	 *		// Returns the plural form of "child"
	 *		$plural = Str::plural('child', 10);
	 *
	 *		// Returns the singular form of "octocat" since count is one
	 *		$plural = Str::plural('octocat', 1);
	 * </code>
	 *
	 * @param  string  $value
	 * @param  int     $count
	 * @return string
	 */
	public static function plural($value, $count = 2)
	{
		return static::pluralizer()->plural($value, $count);
	}

	/**
	 * Get the pluralizer instance.
	 *
	 * @return Pluralizer
	 */
	protected static function pluralizer()
	{
		$config = Config::get('strings');

		return static::$pluralizer ?: static::$pluralizer = new Pluralizer($config);
	}

	/**
	 * Generate a URL friendly "slug" from a given string.
	 *
	 * <code>
	 *		// Returns "this-is-my-blog-post"
	 *		$slug = Str::slug('This is my blog post!');
	 *
	 *		// Returns "this_is_my_blog_post"
	 *		$slug = Str::slug('This is my blog post!', '_');
	 * </code>
	 *
	 * @param  string  $title
	 * @param  string  $separator
	 * @return string
	 */
	public static function slug($title, $separator = '-')
	{
		$title = static::ascii($title);

		// Remove all characters that are not the separator, letters, numbers, or whitespace.
		$title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', static::lower($title));

		// Replace all separator characters and whitespace by a single separator
		$title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

		return trim($title, $separator);
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
	 * Convert a string to an underscored, camel-cased class name.
	 *
	 * This method is primarily used to format task and controller names.
	 *
	 * <code>
	 *		// Returns "Task_Name"
	 *		$class = Str::classify('task_name');
	 *
	 *		// Returns "Taylor_Otwell"
	 *		$class = Str::classify('taylor otwell')
	 * </code>
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function classify($value)
	{
		$search = array('_', '-', '.');

		return str_replace(' ', '_', static::title(str_replace($search, ' ', $value)));
	}

	/**
	 * Return the "URI" style segments in a given string.
	 *
	 * @param  string  $value
	 * @return array
	 */
	public static function segments($value)
	{
		return array_diff(explode('/', trim($value, '/')), array(''));
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
	 * Determine if a given string matches a given pattern.
	 *
	 * @param  string  $pattern
	 * @param  string  $value
	 * @return bool
	 */
	public static function is($pattern, $value)
	{
		// Asterisks are translated into zero-or-more regular expression wildcards
		// to make it convenient to check if the URI starts with a given pattern
		// such as "library/*". This is only done when not root.
		if ($pattern !== '/')
		{
			$pattern = str_replace('*', '(.*)', $pattern).'\z';
		}
		else
		{
			$pattern = '^/$';
		}

		return preg_match('#'.$pattern.'#', $value);
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