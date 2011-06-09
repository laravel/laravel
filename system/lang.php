<?php namespace System;

class Lang {

	/**
	 * All of the loaded language files.
	 *
	 * @var array
	 */
	private static $loaded = array();

	/**
	 * All of the loaded language lines.
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
	 * Get the language line for a given language.
	 *
	 * @param  string  $language
	 * @return string
	 */
	public function get($language = null)
	{
		// --------------------------------------------------------------
		// If no language was specified, use the default language.
		// --------------------------------------------------------------
		if (is_null($language))
		{
			$language = Config::get('application.language');
		}

		// --------------------------------------------------------------
		// Extract the file and item from the key.
		// --------------------------------------------------------------
		list($file, $line) = $this->parse($this->key);

		// --------------------------------------------------------------
		// Load the language file.
		// --------------------------------------------------------------
		$this->load($file, $language);

		// --------------------------------------------------------------
		// Get the language line.
		// --------------------------------------------------------------
		if (array_key_exists($line, static::$lines[$language.$file]))
		{
			$line = static::$lines[$language.$file][$line];
		}
		else
		{
			throw new \Exception("Language line [$line] does not exist for language [$language]");
		}

		// --------------------------------------------------------------
		// Make all place-holder replacements.
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
		// ---------------------------------------------
		// Get the key segments.
		// ---------------------------------------------
		$segments = explode('.', $key);

		// ---------------------------------------------
		// Validate the key format.
		// ---------------------------------------------
		if (count($segments) < 2)
		{
			throw new \Exception("Invalid language key [$key].");
		}

		// ---------------------------------------------
		// Return the file and item name.
		// ---------------------------------------------
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
		// Do not load the file if it has already been loaded.
		// --------------------------------------------------------------
		if (in_array($language.$file, static::$loaded))
		{
			return;
		}

		// --------------------------------------------------------------
		// Does the language file exist?
		// --------------------------------------------------------------
		if (file_exists($path = APP_PATH.'lang/'.$language.'/'.$file.EXT))
		{
			static::$lines[$language.$file] = require $path;
		}
		else
		{
			throw new \Exception("Language file [$file] does not exist for language [$language].");
		}

		// --------------------------------------------------------------
		// Add the file to the array of loaded files.
		// --------------------------------------------------------------
		static::$loaded[] = $language.$file;		
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