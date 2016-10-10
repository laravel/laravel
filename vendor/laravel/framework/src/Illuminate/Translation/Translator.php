<?php namespace Illuminate\Translation;

use Illuminate\Support\Collection;
use Illuminate\Support\NamespacedItemResolver;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\TranslatorInterface;

class Translator extends NamespacedItemResolver implements TranslatorInterface {

	/**
	 * The loader implementation.
	 *
	 * @var \Illuminate\Translation\LoaderInterface
	 */
	protected $loader;

	/**
	 * The default locale being used by the translator.
	 *
	 * @var string
	 */
	protected $locale;

	/**
	 * The fallback locale used by the translator.
	 *
	 * @var string
	 */
	protected $fallback;

	/**
	 * The array of loaded translation groups.
	 *
	 * @var array
	 */
	protected $loaded = array();

	/**
	 * Create a new translator instance.
	 *
	 * @param  \Illuminate\Translation\LoaderInterface  $loader
	 * @param  string  $locale
	 * @return void
	 */
	public function __construct(LoaderInterface $loader, $locale)
	{
		$this->loader = $loader;
		$this->locale = $locale;
	}

	/**
	 * Determine if a translation exists.
	 *
	 * @param  string  $key
	 * @param  string  $locale
	 * @return bool
	 */
	public function has($key, $locale = null)
	{
		return $this->get($key, array(), $locale) !== $key;
	}

	/**
	 * Get the translation for the given key.
	 *
	 * @param  string  $key
	 * @param  array   $replace
	 * @param  string  $locale
	 * @return string
	 */
	public function get($key, array $replace = array(), $locale = null)
	{
		list($namespace, $group, $item) = $this->parseKey($key);

		// Here we will get the locale that should be used for the language line. If one
		// was not passed, we will use the default locales which was given to us when
		// the translator was instantiated. Then, we can load the lines and return.
		foreach ($this->parseLocale($locale) as $locale)
		{
			$this->load($namespace, $group, $locale);

			$line = $this->getLine(
				$namespace, $group, $locale, $item, $replace
			);

			if ( ! is_null($line)) break;
		}

		// If the line doesn't exist, we will return back the key which was requested as
		// that will be quick to spot in the UI if language keys are wrong or missing
		// from the application's language files. Otherwise we can return the line.
		if ( ! isset($line)) return $key;

		return $line;
	}

	/**
	 * Retrieve a language line out the loaded array.
	 *
	 * @param  string  $namespace
	 * @param  string  $group
	 * @param  string  $locale
	 * @param  string  $item
	 * @param  array   $replace
	 * @return string|null
	 */
	protected function getLine($namespace, $group, $locale, $item, array $replace)
	{
		$line = array_get($this->loaded[$namespace][$group][$locale], $item);

		if (is_string($line))
		{
			return $this->makeReplacements($line, $replace);
		}
		elseif (is_array($line) && count($line) > 0)
		{
			return $line;
		}
	}

	/**
	 * Make the place-holder replacements on a line.
	 *
	 * @param  string  $line
	 * @param  array   $replace
	 * @return string
	 */
	protected function makeReplacements($line, array $replace)
	{
		$replace = $this->sortReplacements($replace);

		foreach ($replace as $key => $value)
		{
			$line = str_replace(':'.$key, $value, $line);
		}

		return $line;
	}

	/**
	 * Sort the replacements array.
	 *
	 * @param  array  $replace
	 * @return array
	 */
	protected function sortReplacements(array $replace)
	{
		return with(new Collection($replace))->sortBy(function($r)
		{
			return mb_strlen($r) * -1;
		});
	}

	/**
	 * Get a translation according to an integer value.
	 *
	 * @param  string  $key
	 * @param  int     $number
	 * @param  array   $replace
	 * @param  string  $locale
	 * @return string
	 */
	public function choice($key, $number, array $replace = array(), $locale = null)
	{
		$line = $this->get($key, $replace, $locale = $locale ?: $this->locale);

		$replace['count'] = $number;

		return $this->makeReplacements($this->getSelector()->choose($line, $number, $locale), $replace);
	}

	/**
	 * Get the translation for a given key.
	 *
	 * @param  string  $id
	 * @param  array   $parameters
	 * @param  string  $domain
	 * @param  string  $locale
	 * @return string
	 */
	public function trans($id, array $parameters = array(), $domain = 'messages', $locale = null)
	{
		return $this->get($id, $parameters, $locale);
	}

	/**
	 * Get a translation according to an integer value.
	 *
	 * @param  string  $id
	 * @param  int     $number
	 * @param  array   $parameters
	 * @param  string  $domain
	 * @param  string  $locale
	 * @return string
	 */
	public function transChoice($id, $number, array $parameters = array(), $domain = 'messages', $locale = null)
	{
		return $this->choice($id, $number, $parameters, $locale);
	}

	/**
	 * Load the specified language group.
	 *
	 * @param  string  $namespace
	 * @param  string  $group
	 * @param  string  $locale
	 * @return void
	 */
	public function load($namespace, $group, $locale)
	{
		if ($this->isLoaded($namespace, $group, $locale)) return;

		// The loader is responsible for returning the array of language lines for the
		// given namespace, group, and locale. We'll set the lines in this array of
		// lines that have already been loaded so that we can easily access them.
		$lines = $this->loader->load($locale, $group, $namespace);

		$this->loaded[$namespace][$group][$locale] = $lines;
	}

	/**
	 * Determine if the given group has been loaded.
	 *
	 * @param  string  $namespace
	 * @param  string  $group
	 * @param  string  $locale
	 * @return bool
	 */
	protected function isLoaded($namespace, $group, $locale)
	{
		return isset($this->loaded[$namespace][$group][$locale]);
	}

	/**
	 * Add a new namespace to the loader.
	 *
	 * @param  string  $namespace
	 * @param  string  $hint
	 * @return void
	 */
	public function addNamespace($namespace, $hint)
	{
		$this->loader->addNamespace($namespace, $hint);
	}

	/**
	 * Parse a key into namespace, group, and item.
	 *
	 * @param  string  $key
	 * @return array
	 */
	public function parseKey($key)
	{
		$segments = parent::parseKey($key);

		if (is_null($segments[0])) $segments[0] = '*';

		return $segments;
	}

	/**
	 * Get the array of locales to be checked.
	 *
	 * @return array
	 */
	protected function parseLocale($locale)
	{
		if ( ! is_null($locale))
		{
			return array_filter(array($locale, $this->fallback));
		}
		else
		{
			return array_filter(array($this->locale, $this->fallback));
		}
	}

	/**
	 * Get the message selector instance.
	 *
	 * @return \Symfony\Component\Translation\MessageSelector
	 */
	public function getSelector()
	{
		if ( ! isset($this->selector))
		{
			$this->selector = new MessageSelector;
		}

		return $this->selector;
	}

	/**
	 * Set the message selector instance.
	 *
	 * @param  \Symfony\Component\Translation\MessageSelector  $selector
	 * @return void
	 */
	public function setSelector(MessageSelector $selector)
	{
		$this->selector = $selector;
	}

	/**
	 * Get the language line loader implementation.
	 *
	 * @return \Illuminate\Translation\LoaderInterface
	 */
	public function getLoader()
	{
		return $this->loader;
	}

	/**
	 * Get the default locale being used.
	 *
	 * @return string
	 */
	public function locale()
	{
		return $this->getLocale();
	}

	/**
	 * Get the default locale being used.
	 *
	 * @return string
	 */
	public function getLocale()
	{
		return $this->locale;
	}

	/**
	 * Set the default locale.
	 *
	 * @param  string  $locale
	 * @return void
	 */
	public function setLocale($locale)
	{
		$this->locale = $locale;
	}

	/**
	 * Get the fallback locale being used.
	 *
	 * @return string
	 */
	public function getFallback()
	{
		return $this->fallback;
	}

	/**
	 * Set the fallback locale being used.
	 *
	 * @param  string  $fallback
	 * @return void
	 */
	public function setFallback($fallback)
	{
		$this->fallback = $fallback;
	}

}
