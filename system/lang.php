<?php namespace System;

class Lang {

	/**
	 * All of the loaded language lines.
	 *
	 * The array is keyed by [$language.$file].
	 *
	 * @var array
	 */
	private static $lines = array();

	/**
	 * The key of the line that is being requested.
	 *
	 * @var string
	 */
	private $key;

	/**
	 * The place-holder replacements.
	 *
	 * @var array
	 */
	private $replacements = array();

	/**
	 * Create a new Lang instance.
	 *
	 * @param  string  $line
	 * @return void
	 */
	public function __construct($key)
	{
		$this->key = $key;
	}

	/**
	 * Create a Lang instance for a language line.
	 *
	 * @param  string  $key
	 * @return Lang
	 */
	public static function line($key)
	{
		return new static($key);
	}

	/**
	 * Get the language line.
	 *
	 * @param  mixed   $default
	 * @return string
	 */
	public function get($default = null)
	{
		$language = Config::get('application.language');

		list($file, $line) = $this->parse($this->key);

		$this->load($file, $language);

		// --------------------------------------------------------------
		// If the language file did not exist, return the default value.
		// --------------------------------------------------------------
		if ( ! array_key_exists($language.$file, static::$lines))
		{
			return $default;
		}

		// --------------------------------------------------------------
		// Get the language line from the appropriate file array.
		// If the line doesn't exist, return the default value.
		// --------------------------------------------------------------
		if (array_key_exists($line, static::$lines[$language.$file]))
		{
			$line = static::$lines[$language.$file][$line];
		}
		else
		{
			return $default;
		}

		// --------------------------------------------------------------
		// Make all place-holder replacements. Place-holders are prefixed
		// with a colon for convenient location.
		// --------------------------------------------------------------
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
	 * @return array
	 */
	private function parse($key)
	{
		// --------------------------------------------------------------
		// The left side of the dot is the file name, while the right
		// side of the dot is the item within that file being requested.
		// --------------------------------------------------------------
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
		// --------------------------------------------------------------
		// If we have already loaded the language file or the file
		// doesn't exist, bail out.
		// --------------------------------------------------------------
		if (array_key_exists($language.$file, static::$lines) or ! file_exists($path = APP_PATH.'lang/'.$language.'/'.$file.EXT))
		{
			return;
		}

		static::$lines[$language.$file] = require $path;
	}

	/**
	 * Set the place-holder replacements.
	 *
	 * @param  array  $replacements
	 * @return Lang 
	 */
	public function replace($replacements)
	{
		$this->replacements = $replacements;
		return $this;
	}

}