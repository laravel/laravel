<?php namespace Laravel;

class Lang {

	/**
	 * All of the loaded language lines.
	 *
	 * The array is keyed by [$language.$file].
	 *
	 * @var array
	 */
	public static $lines = array();

	/**
	 * The key of the line that is being requested.
	 *
	 * @var string
	 */
	public $key;

	/**
	 * The language the line should be returned in.
	 *
	 * @var string
	 */
	public $language;

	/**
	 * The place-holder replacements.
	 *
	 * @var array
	 */
	public $replacements = array();

	/**
	 * Create a new Lang instance.
	 *
	 * @param  string  $key
	 * @param  array   $replacements
	 * @return void
	 */
	private function __construct($key, $replacements = array())
	{
		$this->key = $key;
		$this->replacements = $replacements;
		$this->language = Config::get('application.language');
	}

	/**
	 * Create a new Lang instance.
	 *
	 * Language lines are retrieved using "dot" notation. So, asking for the "messages.required" langauge
	 * line would return the "required" line from the "messages" language file.
	 *
	 * <code>
	 *		// Get the "required" line from the "validation" language file
	 *		$line = Lang::line('validation.required')->get();
	 *
	 *		// Specify a replacement for a language line
	 *		$line = Lang::line('welcome.message', array('name' => 'Fred'))->get();
	 * </code>
	 *
	 * @param  string  $key
	 * @param  array   $replacements
	 * @return Lang
	 */
	public static function line($key, $replacements = array())
	{
		return new static($key, $replacements);
	}

	/**
	 * Get the language line.
	 *
	 * A default value may also be specified, which will be returned in the language line doesn't exist.
	 *
	 * <code>
	 *		// Get a validation line and return a default value if the line doesn't exist
	 *		$line = Lang::line('welcome.message')->get('Hello!');
	 * </code>
	 *
	 * @param  string  $language
	 * @return string
	 */
	public function get($default = null)
	{
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
	private function parse($key)
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
	private function load($file)
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

		if (count($language) > 0)
		{
			static::$lines[$this->language.$file] = $language;
		}
		
		return isset(static::$lines[$this->language.$file]);		
	}

	/**
	 * Set the language the line should be returned in.
	 *
	 * The language specified in this method should correspond to a language directory in your application.
	 *
	 * <code>
	 *		// Get a "fr" language line
	 *		$line = Lang::line('validation.required')->in('fr')->get();
	 * </code>
	 *
	 * @param  string  $language
	 * @return Lang
	 */
	public function in($language)
	{
		$this->language = $language;
		return $this;
	}

	/**
	 * Get the string content of the language line.
	 */
	public function __toString()
	{
		return $this->get();
	}

}