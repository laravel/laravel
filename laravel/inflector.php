<?php namespace Laravel;

class Inflector {

	/**
	 * The words that have been converted to singular.
	 *
	 * @var array
	 */
	protected static $singular_cache = array();

	/**
	 * The words that have been converted to plural.
	 *
	 * @var array
	 */
	protected static $plural_cache = array();

	/**
	 * Plural word forms.
	 *
	 * @var array
	 */
	private static $plural = array(
		'/(quiz)$/i' => "$1zes",
		'/^(ox)$/i' => "$1en",
		'/([m|l])ouse$/i' => "$1ice",
		'/(matr|vert|ind)ix|ex$/i' => "$1ices",
		'/(x|ch|ss|sh)$/i' => "$1es",
		'/([^aeiouy]|qu)y$/i' => "$1ies",
		'/(hive)$/i' => "$1s",
		'/(?:([^f])fe|([lr])f)$/i' => "$1$2ves",
		'/(shea|lea|loa|thie)f$/i' => "$1ves",
		'/sis$/i' => "ses",
		'/([ti])um$/i' => "$1a",
		'/(tomat|potat|ech|her|vet)o$/i' => "$1oes",
		'/(bu)s$/i' => "$1ses",
		'/(alias)$/i' => "$1es",
		'/(octop)us$/i' => "$1i",
		'/(ax|test)is$/i' => "$1es",
		'/(us)$/i' => "$1es",
		'/s$/i' => "s",
		'/$/' => "s"
	);

	/**
	 * Singular word forms.
	 *
	 * @var array
	 */
	private static $singular = array(
		'/(quiz)zes$/i' => "$1",
		'/(matr)ices$/i' => "$1ix",
		'/(vert|ind)ices$/i' => "$1ex",
		'/^(ox)en$/i' => "$1",
		'/(alias)es$/i' => "$1",
		'/(octop|vir)i$/i' => "$1us",
		'/(cris|ax|test)es$/i' => "$1is",
		'/(shoe)s$/i' => "$1",
		'/(o)es$/i' => "$1",
		'/(bus)es$/i' => "$1",
		'/([m|l])ice$/i' => "$1ouse",
		'/(x|ch|ss|sh)es$/i' => "$1",
		'/(m)ovies$/i' => "$1ovie",
		'/(s)eries$/i' => "$1eries",
		'/([^aeiouy]|qu)ies$/i' => "$1y",
		'/([lr])ves$/i' => "$1f",
		'/(tive)s$/i' => "$1",
		'/(hive)s$/i' => "$1",
		'/(li|wi|kni)ves$/i' => "$1fe",
		'/(shea|loa|lea|thie)ves$/i' => "$1f",
		'/(^analy)ses$/i' => "$1sis",
		'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => "$1$2sis",
		'/([ti])a$/i' => "$1um",
		'/(n)ews$/i' => "$1ews",
		'/(h|bl)ouses$/i' => "$1ouse",
		'/(corpse)s$/i' => "$1",
		'/(us)es$/i' => "$1",
		'/(us|ss)$/i' => "$1",
		'/s$/i' => "",
	);

	/**
	 * Irregular word forms.
	 *
	 * @var array
	 */
	private static $irregular = array(
		'child' => 'children',
		'foot' => 'feet',
		'goose' => 'geese',
		'man' => 'men',
		'move' => 'moves',
		'person' => 'people',
		'sex' => 'sexes',
		'tooth' => 'teeth',
	);

	/**
	 * Uncountable word forms.
	 *
	 * @var array
	 */
	private static $uncountable = array(
		'audio',
		'equipment',
		'deer',
		'fish',
		'gold',
		'information',
		'money',
		'rice',
		'police',
		'series',
		'sheep',
		'species',
	);

	/**
	 * Convert a word to its plural form.
	 *
	 * Optionally, a count argument may be provided. If the count is greater than
	 * one, the word will be pluralized, otherwise the word will be returned from
	 * the method unchanged.
	 *
	 * <code>
	 *		// Get the plural form of the word "child"
	 *		$children = Inflector::plural('child');
	 *
	 *		// Returns "comments"
	 *		$comments = Inflector::plural('comment', 10);
	 *
	 *		// Returns "comment"
	 *		$comment = Inflector::plural('comment', 1);
	 * </code>
	 *
	 * @param  string  $value
	 * @param  int     $count
	 * @return string
	 */
	public static function plural($value, $count = null)
	{
		if ( ! is_null($count) and $count == 1) return $value;

		$irregular = array_flip(static::$irregular);

		$plural = static::inflect($value, static::$plural_cache, $irregular, static::$plural);

		return static::$plural_cache[$value] = $plural;
	}

	/**
	 * Convert a word to its singular form.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function singular($value)
	{
		$singular = static::inflect($value, static::$singular_cache, static::$irregular, static::$singular);

		return static::$singular_cache[$value] = $singular;
	}

	/**
	 * Convert a word to its singular or plural form.
	 *
	 * @param  string  $value
	 * @param  array   $cache
	 * @param  array   $irregular
	 * @param  array   $source
	 * @return string
	 */
	protected static function inflect($value, $cache, $irregular, $source)
	{
		if (array_key_exists($value, $cache))
		{
			return $cache[$value];
		}

		if (in_array(strtolower($value), static::$uncountable))
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