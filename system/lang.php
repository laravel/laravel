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

		list($file, $line) = $this->parse($this->key);

		$this->load($file, $language);

		if ( ! isset(static::$lines[$language.$file][$line]))
		{
			return is_callable($default) ? call_user_func($default) : $default;
		}

		$line = static::$lines[$language.$file][$line];

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
	 * @return array
	 */
	private function parse($key)
	{
		$segments = explode('.', $key);

		if (count($segments) < 2)
		{
			throw new \Exception("Invalid language key [$key].");
		}

		return array($segments[0], implode('.', array_slice($segments, 1)));
	}

	/**
	 * Load a language file.
	 *
	 * @param  string  $file
	 * @param  string  $language
	 * @return void
	 */
	private function load($file, $language)
	{
		if (array_key_exists($language.$file, static::$lines)) return;

		if (file_exists($path = APP_PATH.'lang/'.$language.'/'.$file.EXT))
		{
			static::$lines[$language.$file] = require $path;
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