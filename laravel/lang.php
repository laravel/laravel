<?php namespace Laravel;

class Lang {

	/**
	 * All of the loaded language lines.
	 *
	 * The array is keyed by [$language.$file].
	 *
	 * @var array
	 */
	protected static $lines = array();

	/**
	 * The key of the language line being retrieved.
	 *
	 * @var string
	 */
	protected $key;

	/**
	 * The replacements that should be made on the language line.
	 *
	 * @var array
	 */
	protected $replacements;

	/**
	 * The language in which the line should be retrieved.
	 *
	 * @var string
	 */
	protected $language;

	/**
	 * The paths containing the language files.
	 *
	 * @var array
	 */
	protected $paths;

	/**
	 * Create a new Lang instance.
	 *
	 * @param  string  $key
	 * @param  array   $replacements
	 * @param  string  $language
	 * @return void
	 */
	protected function __construct($key, $replacements = array(), $language = null)
	{
		$this->key = $key;
		$this->language = $language;
		$this->replacements = $replacements;
	}

	/**
	 * Create a new language line instance.
	 *
	 * @param  string  $key
	 * @param  array   $replacements
	 * @param  string  $language
	 * @return Lang
	 */
	public static function line($key, $replacements = array(), $language = null)
	{
		return new static($key, $replacements, $language);
	}

	/**
	 * Get the language line.
	 *
	 * A default value may also be specified, which will be returned in the language line doesn't exist.
	 *
	 * <code>
	 *		// Retrieve a language line in the default language
	 *		echo Lang::line('validation.required')->get();
	 *
	 *		// Retrieve a language line for a given language
	 *		echo Lang::line('validation.required')->get('sp');
	 *
	 *		// Retrieve a language line and return "Fred" if it doesn't exist
	 *		echo Lang::line('validation.required')->get('en', 'Fred');
	 * </code>
	 *
	 * @param  string  $language
	 * @param  string  $default
	 * @return string
	 */
	public function get($language = null, $default = null)
	{
		if ( ! is_null($language)) $this->language = $language;

		list($file, $line) = $this->parse($this->key);

		if ( ! $this->load($file))
		{
			return ($default instanceof \Closure) ? call_user_func($default) : $default;
		}

		$line = Arr::get(static::$lines[$this->language.$file], $line, $default);

		foreach ($this->replacements as $key => $value)
		{
			$line = str_replace(':'.$key, $value, $line);
		}

		return $line;
	}

	/**
	 * Parse a language key.
	 *
	 * Language keys follow a {file}.{key} convention. If a specific language key is not
	 * specified, an exception will be thrown. Setting entire language files at run-time
	 * is not currently supported.
	 *
	 * @param  string  $key
	 * @return array
	 */
	protected function parse($key)
	{
		if (count($segments = explode('.', $key)) > 1)
		{
			return array($segments[0], implode('.', array_slice($segments, 1)));
		}

		throw new \Exception("Invalid language line [$key]. A specific line must be specified.");
	}

	/**
	 * Load a language file.
	 *
	 * If the language file has already been loaded, it will not be loaded again.
	 *
	 * @param  string  $file
	 * @return bool
	 */
	protected function load($file)
	{
		if (isset(static::$lines[$this->language.$file])) return;

		$language = array();

		foreach (array(SYS_LANG_PATH, LANG_PATH) as $directory)
		{
			if (file_exists($path = $directory.$this->language.'/'.$file.EXT))
			{
				$language = array_merge($language, require $path);
			}
		}

		if (count($language) > 0) static::$lines[$this->language.$file] = $language;
		
		return isset(static::$lines[$this->language.$file]);		
	}

	/**
	 * Get the string content of the language line.
	 *
	 * This provides a convenient mechanism for displaying language line in views without
	 * using the "get" method on the language instance.
	 *
	 * <code>
	 *		// Display a language line by casting it to a string
	 *		echo Lang::line('messages.welcome');
	 * </code>
	 *
	 */
	public function __toString()
	{
		return $this->get();
	}

}