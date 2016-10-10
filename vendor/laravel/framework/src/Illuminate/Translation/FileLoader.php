<?php namespace Illuminate\Translation;

use Illuminate\Filesystem\Filesystem;

class FileLoader implements LoaderInterface {

	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * The default path for the loader.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * All of the namespace hints.
	 *
	 * @var array
	 */
	protected $hints = array();

	/**
	 * Create a new file loader instance.
	 *
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @param  string  $path
	 * @return void
	 */
	public function __construct(Filesystem $files, $path)
	{
		$this->path = $path;
		$this->files = $files;
	}

	/**
	 * Load the messages for the given locale.
	 *
	 * @param  string  $locale
	 * @param  string  $group
	 * @param  string  $namespace
	 * @return array
	 */
	public function load($locale, $group, $namespace = null)
	{
		if (is_null($namespace) || $namespace == '*')
		{
			return $this->loadPath($this->path, $locale, $group);
		}
		else
		{
			return $this->loadNamespaced($locale, $group, $namespace);
		}
	}

	/**
	 * Load a namespaced translation group.
	 *
	 * @param  string  $locale
	 * @param  string  $group
	 * @param  string  $namespace
	 * @return array
	 */
	protected function loadNamespaced($locale, $group, $namespace)
	{
		if (isset($this->hints[$namespace]))
		{
			$lines = $this->loadPath($this->hints[$namespace], $locale, $group);

			return $this->loadNamespaceOverrides($lines, $locale, $group, $namespace);
		}

		return array();
	}

	/**
	 * Load a local namespaced translation group for overrides.
	 *
	 * @param  array  $lines
	 * @param  string  $locale
	 * @param  string  $group
	 * @param  string  $namespace
	 * @return array
	 */
	protected function loadNamespaceOverrides(array $lines, $locale, $group, $namespace)
	{
		$file = "{$this->path}/packages/{$locale}/{$namespace}/{$group}.php";

		if ($this->files->exists($file))
		{
			return array_replace_recursive($lines, $this->files->getRequire($file));
		}

		return $lines;
	}

	/**
	 * Load a locale from a given path.
	 *
	 * @param  string  $path
	 * @param  string  $locale
	 * @param  string  $group
	 * @return array
	 */
	protected function loadPath($path, $locale, $group)
	{
		if ($this->files->exists($full = "{$path}/{$locale}/{$group}.php"))
		{
			return $this->files->getRequire($full);
		}

		return array();
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
		$this->hints[$namespace] = $hint;
	}

}
