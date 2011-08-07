<?php namespace System;

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
		if (is_null($language))
		{
			$language = Config::get('application.language');
		}

		list($module, $file, $line) = $this->parse($this->key, $language);

		$this->load($module, $file, $language);

		if ( ! isset(static::$lines[$module][$language.$file][$line]))
		{
			return is_callable($default) ? call_user_func($default) : $default;
		}

		$line = static::$lines[$module][$language.$file][$line];

		foreach ($this->replacements as $key => $value)
		{
			$line = str_replace(':'.$key, $value, $line);
		}

		return $line;
	}

	/**
	 * Parse a language key.
	 *
	 * The value on the left side of the dot is the language file name,
	 * while the right side of the dot is the item within that file.
	 *	 
	 * @param  string  $key
	 * @param  string  $language
	 * @return array
	 */
	private function parse($key, $language)
	{
		// Check for a module qualifier. If a module name is present, we need to extract it from
		// the language line, otherwise, we will use "application" as the module.
		$module = (strpos($key, '::') !== false) ? substr($key, 0, strpos($key, ':')) : 'application';

		if ($module != 'application')
		{
			$key = substr($key, strpos($key, ':') + 2);
		}

		$segments = explode('.', $key);

		if (count($segments) > 1)
		{
			return array($module, $segments[0], $segments[1]);
		}

		throw new \Exception("Invalid language line [$key]. A specific line must be specified.");
	}

	/**
	 * Load a language file.
	 *
	 * @param  string  $module
	 * @param  string  $file
	 * @param  string  $language
	 * @return void
	 */
	private function load($module, $file, $language)
	{
		if (isset(static::$lines[$module][$language.$file])) return;

		$path = ($module === 'application') ? LANG_PATH : MODULE_PATH.$module.'/lang/';

		if (file_exists($path = $path.$language.'/'.$file.EXT))
		{
			static::$lines[$module][$language.$file] = require $path;
		}
	}

	/**
	 * Get the string content of the language line.
	 */
	public function __toString()
	{
		return $this->get();
	}

}