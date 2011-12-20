<?php namespace Laravel; use Closure;

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
	 *		$line = Lang::line('validation.required', null, 'Default');
	 * </code>
	 *
	 * @param  string  $language
	 * @param  string  $default
	 * @return string
	 */
	public function get($language = null, $default = null)
	{
		if (is_null($language)) $language = $this->language;

		list($bundle, $file, $line) = $this->parse($this->key);

		// If the file doesn't exist, we'll just return the default value that was
		// given to the method. The default value is also returned even when the
		// file exists if the file does not actually contain any lines.
		if ( ! static::load($bundle, $language, $file))
		{
			return ($default instanceof Closure) ? call_user_func($default) : $default;
		}


		$lines = static::$lines[$bundle][$language][$file];

		$line = array_get($lines, $line, $default);

		// If the requested line is a string, we will replace all of the line's
		// place-holders with the replacements values that were specified for
		// the line when it was created. Each replacement place-holder is
		// prefixed with a colon for easy matching.
		//
		// If the line is not a string, it probably means the developer asked
		// for the entire langauge file and the value of the requsted value
		// will be an array of all the lines.
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
			return array($bundle, $segments[0], implode('.', array_slice($segments, 1)));
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
		if (isset(static::$lines[$bundle][$language][$file])) return;

		$lines = array();

		if (file_exists($path = Bundle::path($bundle).'language/'.$language.'/'.$file.EXT))
		{
			$lines = require $path;
		}

		static::$lines[$bundle][$language][$file] = $lines;

		return count($lines) > 0;
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