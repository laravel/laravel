<?php namespace Laravel;

class Lang extends Facade { public static $resolve = 'lang'; }

class Lang_Engine {

	/**
	 * All of the loaded language lines.
	 *
	 * The array is keyed by [$language.$file].
	 *
	 * @var array
	 */
	private $lines = array();

	/**
	 * The default language being used by the application.
	 *
	 * @var string
	 */
	private $language;

	/**
	 * The paths containing the language files.
	 *
	 * @var array
	 */
	private $paths;

	/**
	 * The key of the language line being retrieved.
	 *
	 * @var string
	 */
	private $key;

	/**
	 * The replacements that should be made on the language line.
	 *
	 * @var array
	 */
	private $replacements;

	/**
	 * The language of the line being retrieved.
	 *
	 * This is set to the default language when a new line is requested.
	 * However, it may be changed using the "in" method.
	 *
	 * @var string
	 */
	private $line_language;

	/**
	 * Create a new Lang instance.
	 *
	 * @param  string  $language
	 * @param  array   $paths
	 * @return void
	 */
	public function __construct($language, $paths)
	{
		$this->paths = $paths;
		$this->language = $language;
	}

	/**
	 * Begin retrieving a new language line.
	 *
	 * Language lines are retrieved using "dot" notation. So, asking for the "messages.required" langauge
	 * line would return the "required" line from the "messages" language file.
	 *
	 * @param  string  $key
	 * @param  array   $replacements
	 * @return Lang
	 */
	public function line($key, $replacements = array())
	{
		$this->key = $key;
		$this->replacements = $replacements;
		$this->line_language = $this->language;

		return $this;
	}

	/**
	 * Get the language line.
	 *
	 * A default value may also be specified, which will be returned in the language line doesn't exist.
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

		$line = Arr::get($this->lines[$this->line_language.$file], $line, $default);

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
		if (isset($this->lines[$this->line_language.$file])) return;

		$language = array();

		foreach ($this->paths as $directory)
		{
			if (file_exists($path = $directory.$this->line_language.'/'.$file.EXT))
			{
				$language = array_merge($language, require $path);
			}
		}

		if (count($language) > 0)
		{
			$this->lines[$this->line_language.$file] = $language;
		}
		
		return isset($this->lines[$this->line_language.$file]);		
	}

	/**
	 * Set the language the line should be returned in.
	 *
	 * The language specified in this method should correspond to a language directory in your application.
	 *
	 * @param  string  $language
	 * @return Lang
	 */
	public function in($language)
	{
		$this->line_language = $line_language;
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