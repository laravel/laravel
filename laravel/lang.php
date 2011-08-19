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
	 * The place-holder replacements.
	 *
	 * @var array
	 */
	public $replacements = array();

	/**
	 * Create a new Lang instance.
	 *
	 * Language lines are retrieved using "dot" notation. So, asking for the
	 * "messages.required" language line would return the "required" line
	 * from the "messages" language file.	 
	 *
	 * @param  string  $key
	 * @param  array   $replacements
	 * @return void
	 */
	public function __construct($key, $replacements = array())
	{
		$this->key = $key;
		$this->replacements = $replacements;
	}

	/**
	 * Create a Lang instance for a language line.
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
	 * @param  string  $language
	 * @param  mixed   $default
	 * @return string
	 */
	public function get($language = null, $default = null)
	{
		if (is_null($language)) $language = Config::get('application.language');

		list($module, $file, $line) = $this->parse($this->key, $language);

		if ( ! $this->load($module, $file, $language))
		{
			return is_callable($default) ? call_user_func($default) : $default;
		}

		$line = Arr::get(static::$lines[$module][$language.$file], $line, $default);

		foreach ($this->replacements as $key => $value)
		{
			$line = str_replace(':'.$key, $value, $line);
		}

		return $line;
	}

	/**
	 * Parse a language key.
	 *
	 * @param  string  $key
	 * @param  string  $language
	 * @return array
	 */
	private function parse($key, $language)
	{
		list($module, $key) = Module::parse($key);

		if (count($segments = explode('.', $key)) > 1)
		{
			return array($module, $segments[0], implode('.', array_slice($segments, 1)));
		}

		throw new \Exception("Invalid language line [$key]. A specific line must be specified.");
	}

	/**
	 * Load a language file.
	 *
	 * @param  string  $module
	 * @param  string  $file
	 * @param  string  $language
	 * @return bool
	 */
	private function load($module, $file, $language)
	{
		if (isset(static::$lines[$module][$language.$file])) return;

		$lang = array();

		foreach (array(LANG_PATH, Module::path($module).'lang/') as $directory)
		{
			$lang = (file_exists($path = $directory.$language.'/'.$file.EXT)) ? array_merge($lang, require $path) : $lang;
		}

		if (count($lang) > 0) static::$lines[$module][$language.$file] = $lang;
		
		return isset(static::$lines[$module][$language.$file]);		
	}

	/**
	 * Get the string content of the language line.
	 */
	public function __toString()
	{
		return $this->get();
	}

}