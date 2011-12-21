<?php namespace Laravel;

class Inflector {

	/**
	 * A cache of the inflected words.
	 *
	 * @var array
	 */
	protected static $cache = array('singular' => array(), 'plural' => array());

	/**
	 * Convert a word to its plural form.
	 *
	 * <code>
	 *		// Get the plural form of the word "child"
	 *		$children = Inflector::plural('child');
	 *
	 *		// Returns "comment"
	 *		$comment = Inflector::plural('comment', 1);
	 *
	 *		// Returns "comments"
	 *		$comments = Inflector::plural('comment', 10);
	 * </code>
	 *
	 * @param  string  $value
	 * @param  int     $count
	 * @return string
	 */
	public static function plural($value, $count = null)
	{
		if ( ! is_null($count) and $count == 1) return $value;

		$cache =& static::$cache['plural'];

		$plural = static::inflect($value, $cache, Config::get('inflector.plural'), array_flip(Config::get('inflector.irregular')));

		return $cache[$value] = $plural;
	}

	/**
	 * Convert a word to its singular form.
	 *
	 * <code>
	 *		// Get the singular form of the word "children"
	 *		$child = Inflector::singular('children');
	 * </code>
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function singular($value)
	{
		$cache =& static::$cache['singular'];

		$singular = static::inflect($value, $cache, Config::get('inflector.singular'), Config::get('inflector.irregular'));

		return $cache[$value] = $singular;
	}

	/**
	 * Convert a word to its singular or plural form.
	 *
	 * @param  string  $value
	 * @param  array   $cache
	 * @param  array   $source
	 * @param  array   $irregular
	 * @return string
	 */
	protected static function inflect($value, $cache, $source, $irregular)
	{
		if (array_key_exists($value, $cache))
		{
			return $cache[$value];
		}

		if (in_array(strtolower($value), Config::get('inflector.uncountable')))
		{
			return $value;
		}

		foreach ($irregular as $irregular => $pattern)
		{
			if (preg_match($pattern = '/'.$pattern.'$/i', $value))
			{
				return preg_replace($pattern, $irregular, $value);
			}
		}

		foreach ($source as $pattern => $inflected)
		{
			if (preg_match($pattern, $value))
			{
				return preg_replace($pattern, $inflected, $value);
			}
		}

		return $value;
	}

}