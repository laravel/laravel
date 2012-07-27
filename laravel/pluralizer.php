<?php namespace Laravel;

class Pluralizer {

	/**
	 * The "strings" configuration array.
	 *
	 * @var array
	 */
	protected $config;

	/**
	 * The cached copies of the plural inflections.
	 */
	protected $plural = array();

	/**
	 * The cached copies of the singular inflections.
	 *
	 * @var array
	 */
	protected $singular = array();

	/**
	 * Create a new pluralizer instance.
	 *
	 * @return void
	 */
	public function __construct($config)
	{
		$this->config = $config;
	}

	/**
	 * Get the singular form of the given word.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function singular($value)
	{
		// First we'll check the cache of inflected values. We cache each word that
		// is inflected so we don't have to spin through the regular expressions
		// each time we need to inflect a given value for the developer.
		if (isset($this->singular[$value]))
		{
			return $this->singular[$value];
		}

		// English words may be automatically inflected using regular expressions.
		// If the word is English, we'll just pass off the word to the automatic
		// inflection method and return the result, which is cached.
		$irregular = $this->config['irregular'];

		$result = $this->auto($value, $this->config['singular'], $irregular);

		return $this->singular[$value] = $result ?: $value;
	}

	/**
	 * Get the plural form of the given word.
	 *
	 * @param  string  $value
	 * @param  int     $count
	 * @return string
	 */
	public function plural($value, $count = 2)
	{
		if ((int) $count == 1) return $value;

		// First we'll check the cache of inflected values. We cache each word that
		// is inflected so we don't have to spin through the regular expressions
		// each time we need to inflect a given value for the developer.
		if (isset($this->plural[$value]))
		{
			return $this->plural[$value];
		}

		// English words may be automatically inflected using regular expressions.
		// If the word is English, we'll just pass off the word to the automatic
		// inflection method and return the result, which is cached.
		$irregular = array_flip($this->config['irregular']);

		$result = $this->auto($value, $this->config['plural'], $irregular);

		return $this->plural[$value] = $result;
	}

	/**
	 * Perform auto inflection on an English word.
	 *
	 * @param  string  $value
	 * @param  array   $source
	 * @param  array   $irregular
	 * @return string
	 */
	protected function auto($value, $source, $irregular)
	{
		// If the word hasn't been cached, we'll check the list of words that
		// that are "uncountable". This should be a quick look up since we
		// can just hit the array directly for the value.
		if (in_array(Str::lower($value), $this->config['uncountable']))
		{
			return $value;
		}

		// Next, we will check the "irregular" patterns, which contain words
		// like "children" and "teeth" which can not be inflected using the
		// typically used regular expression matching approach.
		foreach ($irregular as $irregular => $pattern)
		{
			if (preg_match($pattern = '/'.$pattern.'$/i', $value))
			{
				return preg_replace($pattern, $irregular, $value);
			}
		}

		// Finally we'll spin through the array of regular expressions and
		// and look for matches for the word. If we find a match we will
		// cache and return the inflected value for quick look up.
		foreach ($source as $pattern => $inflected)
		{
			if (preg_match($pattern, $value))
			{
				return preg_replace($pattern, $inflected, $value);
			}
		}
	}

}