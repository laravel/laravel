<?php namespace System;

class Inflector {

    /**
     * The words that have been converted to singular.
     *
     * @var array
     */
    private static $singular_cache = array();

    /**
     * The words that have been converted to plural.
     *
     * @var array
     */
    private static $plural_cache = array();

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
        'move' => 'moves',
        'foot' => 'feet',
        'goose' => 'geese',
        'sex' => 'sexes',
        'child' => 'children',
        'man' => 'men',
        'tooth' => 'teeth',
        'person' => 'people',
    );

    /**
     * Uncountable word forms.
     *
     * @var array
     */
    private static $uncountable = array(
        'sheep',
        'fish',
        'deer',
        'series',
        'species',
        'money',
        'rice',
        'information',
        'equipment',
    );

	/**
	 * Convert a word to its plural form.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function plural($value)
	{
        if (array_key_exists($value, static::$plural_cache))
        {
           return static::$plural_cache[$value];
        }

        if (in_array(strtolower($value), static::$uncountable))
        {
        	return static::$plural_cache[$value] = $value;
        }

        foreach (static::$irregular as $pattern => $irregular)
        {
            $pattern = '/'.$pattern.'$/i';

            if (preg_match($pattern, $value))
            {
        		return static::$plural_cache[$value] = preg_replace($pattern, $irregular, $value);
            }
        }

        foreach (static::$plural as $pattern => $plural)
        {
            if (preg_match($pattern, $value))
            {
        		return static::$plural_cache[$value] = preg_replace($pattern, $plural, $value);
            }
        }

        return static::$plural_cache[$value] = $value;
	}

	/**
	 * Convert a word to its singular form.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function singular($value)
	{
        if (array_key_exists($value, static::$singular_cache))
        {
           return static::$singular_cache[$value];
        }

        if (in_array(strtolower($value), static::$uncountable))
        {
        	return static::$singular_cache[$value] = $value;
        }

        foreach (static::$irregular as $irregular => $pattern)
        {
            $pattern = '/'.$pattern.'$/i';

            if (preg_match($pattern, $value))
            {
				return static::$singular_cache[$value] = preg_replace($pattern, $irregular, $value);
            }
        }

        foreach (static::$singular as $pattern => $singular)
        {
            if (preg_match($pattern, $value))
            {
				return static::$singular_cache[$value] = preg_replace($pattern, $singular, $value);
            }
        }

        return static::$singular_cache[$value] = $value;
	}

	/**
	 * Get the plural form of a word if the count is greater than zero.
	 *
	 * @param  string  $value
	 * @param  int     $count
	 * @return string
	 */
	public static function plural_if($value, $count)
	{
		return ($count > 1) ? static::plural($value) : $value;
	}

}