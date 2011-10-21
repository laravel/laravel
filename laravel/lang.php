<?php namespace Laravel; use Closure;

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
	 * The paths containing the language files.
	 *
	 * @var array
	 */
	protected static $paths = array(LANG_PATH);

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
	 * <code>
	 *		// Create a new language line instance for a given line
	 *		$line = Lang::line('validation.required');
	 *
	 *		// Specify some replacements for the language line
	 *		$line = Lang::line('validation.required', array('attribute' => 'email'));
	 * </code>
	 *
	 * @param  string  $key
	 * @param  array   $replacements
	 * @param  string  $language
	 * @return Lang
	 */
	public static function line($key, $replacements = array(), $language = null)
	{
		if (is_null($language)) $language = Config::$items['application']['language'];

		return new static($key, $replacements, $language);
	}

	/**
	 * Get the language line as a string.
	 *
	 * If a language is specified, it should correspond to a directory
	 * within your application language directory.
	 *
	 * <code>
	 *		// Get a language line
	 *		$line = Lang::line('validation.required')->get();
	 *
	 *		// Get a language line in a specified language
	 *		$line = Lang::line('validation.required')->get('sp');
	 *
	 *		// Return a default value if the line doesn't exist
	 *		$line = Lang::line('validation.required', null, 'Default');
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
			return ($default instanceof Closure) ? call_user_func($default) : $default;
		}

		return $this->replace(Arr::get(static::$lines[$this->language.$file], $line, $default));
	}

	/**
	 * Make all necessary replacements on a language line.
	 *
	 * Replacements place-holder are prefixed with a colon, and are replaced
	 * with the appropriate value based on the replacement array set for the
	 * language line instance.
	 *
	 * @param  string  $line
	 * @return string
	 */
	protected function replace($line)
	{
		foreach ($this->replacements as $key => $value)
		{
			$line = str_replace(':'.$key, $value, $line);
		}

		return $line;
	}

	/**
	 * Parse a language key into its file and line segments.
	 *
	 * Language keys are formatted similarly to configuration keys. The first
	 * segment represents the language file, while the second segment
	 * represents a language line within that file.
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
	 * Load all of the language lines from a language file.
	 *
	 * @param  string  $file
	 * @return bool
	 */
	protected function load($file)
	{
		if (isset(static::$lines[$this->language.$file])) return true;

		$language = array();

		foreach (static::$paths as $directory)
		{
			if (file_exists($path = $directory.$this->language.'/'.$file.EXT))
			{
				$language = array_merge($language, require $path);
			}
		}

		// If language lines were actually found, they will be loaded into
		// the array containing all of the lines for all languages and files.
		// The array is keyed by the language and the file name.
		if (count($language) > 0) static::$lines[$this->language.$file] = $language;
		
		return isset(static::$lines[$this->language.$file]);		
	}

	/**
	 * Get the string content of the language line.
	 */
	public function __toString()
	{
		return $this->get();
	}

}