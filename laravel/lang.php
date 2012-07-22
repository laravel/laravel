<?php namespace Laravel;

class Lang {

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
	 * All of the loaded language lines.
	 *
	 * The array is keyed by [$bundle][$language][$file].
	 *
	 * @var array
	 */
	protected static $lines = array();

	/**
	 * The language loader event name.
	 *
	 * @var string
	 */
	const loader = 'laravel.language.loader';

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
		$this->replacements = (array) $replacements;
	}

	/**
	 * Create a new language line instance.
	 *
	 * <code>
	 *		// Create a new language line instance for a given line
	 *		$line = Lang::line('validation.required');
	 *
	 *		// Create a new language line for a line belonging to a bundle
	 *		$line = Lang::line('admin::messages.welcome');
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
		if (is_null($language)) $language = Config::get('application.language');

		return new static($key, $replacements, $language);
	}

	/**
	 * Determine if a language line exists.
	 *
	 * @param  string  $key
	 * @param  string  $language
	 * @return bool
	 */
	public static function has($key, $language = null)
	{
		return static::line($key, array(), $language)->get() !== $key;
	}

	/**
	 * Get the language line as a string.
	 *
	 * <code>
	 *		// Get a language line
	 *		$line = Lang::line('validation.required')->get();
	 *
	 *		// Get a language line in a specified language
	 *		$line = Lang::line('validation.required')->get('sp');
	 *
	 *		// Return a default value if the line doesn't exist
	 *		$line = Lang::line('validation.required')->get(null, 'Default');
	 * </code>
	 *
	 * @param  string  $language
	 * @param  string  $default
	 * @return string
	 */
	public function get($language = null, $default = null)
	{
		// If no default value is specified by the developer, we'll just return the
		// key of the language line. This should indicate which language line we
		// were attempting to render and is better than giving nothing back.
		if (is_null($default)) $default = $this->key;

		if (is_null($language)) $language = $this->language;

		list($bundle, $file, $line) = $this->parse($this->key);

		// If the file does not exist, we'll just return the default value that was
		// given to the method. The default value is also returned even when the
		// file exists and that file does not actually contain any lines.
		if ( ! static::load($bundle, $language, $file))
		{
			return value($default);
		}

		$lines = static::$lines[$bundle][$language][$file];

		$line = array_get($lines, $line, $default);

		// If the line is not a string, it probably means the developer asked for
		// the entire language file and the value of the requested value will be
		// an array containing all of the lines in the file.
		if (is_string($line))
		{
			foreach ($this->replacements as $key => $value)
			{
				$line = str_replace(':'.$key, $value, $line);
			}
		}

		return $line;
	}

	/**
	 * Parse a language key into its bundle, file, and line segments.
	 *
	 * Language lines follow a {bundle}::{file}.{line} naming convention.
	 *
	 * @param  string  $key
	 * @return array
	 */
	protected function parse($key)
	{
		$bundle = Bundle::name($key);

		$segments = explode('.', Bundle::element($key));

		// If there are not at least two segments in the array, it means that
		// the developer is requesting the entire language line array to be
		// returned. If that is the case, we'll make the item "null".
		if (count($segments) >= 2)
		{
			$line = implode('.', array_slice($segments, 1));

			return array($bundle, $segments[0], $line);
		}
		else
		{
			return array($bundle, $segments[0], null);
		}
	}

	/**
	 * Load all of the language lines from a language file.
	 *
	 * @param  string  $bundle
	 * @param  string  $language
	 * @param  string  $file
	 * @return bool
	 */
	public static function load($bundle, $language, $file)
	{
		if (isset(static::$lines[$bundle][$language][$file]))
		{
			return true;
		}

		// We use a "loader" event to delegate the loading of the language
		// array, which allows the develop to organize the language line
		// arrays for their application however they wish.
		$lines = Event::first(static::loader, func_get_args());

		static::$lines[$bundle][$language][$file] = $lines;

		return count($lines) > 0;
	}

	/**
	 * Load a language array from a language file.
	 *
	 * @param  string  $bundle
	 * @param  string  $language
	 * @param  string  $file
	 * @return array
	 */
	public static function file($bundle, $language, $file)
	{
		$lines = array();

		// Language files can belongs to the application or to any bundle
		// that is installed for the application. So, we'll need to use
		// the bundle's path when looking for the file.
		$path = static::path($bundle, $language, $file);

		if (file_exists($path))
		{
			$lines = require $path;
		}

		return $lines;
	}

	/**
	 * Get the path to a bundle's language file.
	 *
	 * @param  string  $bundle
	 * @param  string  $language
	 * @param  string  $file
	 * @return string
	 */
	protected static function path($bundle, $language, $file)
	{
		return Bundle::path($bundle)."language/{$language}/{$file}".EXT;
	}

	/**
	 * Get the string content of the language line.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->get();
	}

}
