<?php namespace System;

use System\File;
use System\HTML;

class Asset {

	/**
	 * All of the asset containers. Asset containers are created through the
	 * container method, and are managed as singletons.
	 *
	 * @var array
	 */
	public static $containers = array();

	/**
	 * Get an asset container instance.
	 *
	 * If no container name is specified, the default container will be returned.
	 * Containers provide a convenient method of grouping assets while maintaining
	 * expressive code and a clean API.
	 *
	 * <code>
	 * // Get the default asset container
	 * $container = Asset::container();
	 *
	 * // Get the "footer" asset contanier
	 * $container = Asset::container('footer');
	 *
	 * // Add an asset to the "footer" container
	 * Asset::container('footer')->add('jquery', 'js/jquery.js');
	 * </code>
	 *
	 * @param  string            $container
	 * @return Asset_Container
	 */
	public static function container($container = 'default')
	{
		if ( ! isset(static::$containers[$container]))
		{
			static::$containers[$container] = new Asset_Container($container);
		}

		return static::$containers[$container];
	}

	/**
	 * Magic Method for calling methods on the default Asset container.
	 * This allows a convenient API for working with the default container.
	 *
	 * <code>
	 * // Add jQuery to the default container
	 * Asset::script('jquery', 'js/jquery.js');
	 *
	 * // Equivalent call using the container method
	 * Asset::container()->script('jquery', 'js/jquery.js');
	 * </code>
	 */
	public static function __callStatic($method, $parameters)
	{
		return call_user_func_array(array(static::container(), $method), $parameters);
	}

}

class Asset_Container {

	/**
	 * The asset container name.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * All of the registered assets.
	 *
	 * @var array
	 */
	public $assets = array();

	/**
	 * Create a new asset container instance.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function __construct($name)
	{
		$this->name = $name;
	}

	/**
	 * Add an asset to the container.
	 *
	 * The extension of the asset source will be used to determine the type of
	 * asset being registered (CSS or JavaScript). If you are using a non-standard
	 * extension, you may use the style or script methods to register assets.
	 *
	 * <code>
	 * // Register a jQuery asset
	 * Asset::add('jquery', 'js/jquery.js');
	 * </code>
	 *
	 * You may also specify asset dependencies. This will instruct the class to
	 * only link to the registered asset after its dependencies have been linked.
	 * For example, you may wish to make jQuery UI dependent on jQuery.
	 *
	 * <code>
	 * // Register jQuery UI as dependent on jQuery
	 * Asset::add('jquery-ui', 'js/jquery-ui.js', 'jquery');
	 *
	 * // Register jQuery UI with multiple dependencies
	 * Asset::add('jquery-ui', 'js/jquery-ui.js', array('jquery', 'fader'));
	 * </code>
	 *
	 * @param  string  $name
	 * @param  string  $source
	 * @param  array   $dependencies
	 * @param  array   $attributes
	 * @return void
	 */
	public function add($name, $source, $dependencies = array(), $attributes = array())
	{
		// Since assets may contain timestamps to force a refresh, we will strip them
		// off to get the "real" filename of the asset.
		$segments = explode('?', $source);

		$type = (File::extension($segments[0]) == 'css') ? 'style' : 'script';

		return call_user_func(array($this, $type), $name, $source, $dependencies, $attributes);
	}

	/**
	 * Add CSS to the registered assets.
	 *
	 * @param  string  $name
	 * @param  string  $source
	 * @param  array   $dependencies
	 * @param  array   $attributes
	 * @return void
	 * @see    add
	 */
	public function style($name, $source, $dependencies = array(), $attributes = array())
	{
		if ( ! array_key_exists('media', $attributes))
		{
			$attributes['media'] = 'all';
		}

		$this->register('style', $name, $source, $dependencies, $attributes);
	}

	/**
	 * Add JavaScript to the registered assets.
	 *
	 * @param  string  $name
	 * @param  string  $source
	 * @param  array   $dependencies
	 * @param  array   $attributes
	 * @return void
	 * @see    add
	 */
	public function script($name, $source, $dependencies = array(), $attributes = array())
	{
		$this->register('script', $name, $source, $dependencies, $attributes);
	}

	/**
	 * Add an asset to the registered assets.
	 *
	 * @param  string  $type
	 * @param  string  $name
	 * @param  string  $source
	 * @param  array   $dependencies
	 * @param  array   $attributes
	 * @return void
	 */
	private function register($type, $name, $source, $dependencies, $attributes)
	{
		$dependencies = (array) $dependencies;

		$this->assets[$type][$name] = compact('source', 'dependencies', 'attributes');
	}

	/**
	 * Get the links to all of the registered CSS assets.
	 *
	 * @return  string
	 */
	public function styles()
	{
		return $this->get_group('style');
	}

	/**
	 * Get the links to all of the registered JavaScript assets.
	 *
	 * @return  string
	 */
	public function scripts()
	{
		return $this->get_group('script');
	}

	/**
	 * Get all of the registered assets for a given group.
	 *
	 * @param  string  $group
	 * @return string
	 */
	private function get_group($group)
	{
		if ( ! isset($this->assets[$group]) or count($this->assets[$group]) == 0) return '';

		$assets = '';

		foreach ($this->arrange($this->assets[$group]) as $name => $data)
		{
			$assets .= $this->get_asset($group, $name);
		}
		
		return $assets;
	}

	/**
	 * Get the link to a single registered CSS asset.
	 *
	 * <code>
	 * echo $container->get_style('common');
	 * </code>
	 *
	 * @param  string  $name
	 * @return string
	 */
	public function get_style($name)
	{
		return $this->get_asset('style', $name);
	}

	/**
	 * Get the link to a single registered JavaScript asset.
	 *
	 * <code>
	 * echo $container->get_script('jquery');
	 * </code>
	 *
	 * @param  string  $name
	 * @return string
	 */
	public function get_script($name)
	{
		return $this->get_asset('script', $name);
	}

	/**
	 * Get a registered asset.
	 *
	 * @param  string  $group
	 * @param  string  $name
	 * @return string
	 */
	private function get_asset($group, $name)
	{
		if ( ! isset($this->assets[$group][$name])) return '';

		$asset = $this->assets[$group][$name];

		return HTML::$group($asset['source'], $asset['attributes']);
	}

	/**
	 * Sort and retrieve assets based on their dependencies
	 *
	 * @param   array  $assets
	 * @return  array
	 */
	private function arrange($assets)
	{
		list($original, $sorted) = array($assets, array());

		while (count($assets) > 0)
		{
			foreach ($assets as $asset => $value)
			{
				$this->evaluate_asset($asset, $value, $original, $sorted, $assets);
			}
		}
		
		return $sorted;
	}

	/**
	 * Evaluate an asset and its dependencies.
	 *
	 * @param  string  $asset
	 * @param  string  $value
	 * @param  array   $original
	 * @param  array   $sorted
	 * @param  array   $assets
	 * @return void
	 */
	private function evaluate_asset($asset, $value, $original, &$sorted, &$assets)
	{
		// If the asset has no more dependencies, we can add it to the sorted list
		// and remove it from the array of assets. Otherwise, we will not verify
		// the asset's dependencies and determine if they have already been sorted.
		if (count($assets[$asset]['dependencies']) == 0)
		{
			$sorted[$asset] = $value;
			unset($assets[$asset]);
		}
		else
		{
			foreach ($assets[$asset]['dependencies'] as $key => $dependency)
			{
				if ( ! $this->dependency_is_valid($asset, $dependency, $original, $assets))
				{
					unset($assets[$asset]['dependencies'][$key]);
					continue;
				}

				// If the dependency has not yet been added to the sorted list, we can not
				// remove it from this asset's array of dependencies. We'll try again on
				// the next trip through the loop.
				if ( ! isset($sorted[$dependency])) continue;

				unset($assets[$asset]['dependencies'][$key]);
			}
		}		
	}

	/**
	 * Check that a dependency is valid.
	 *
	 * @param  string  $asset
	 * @param  string  $dependency
	 * @param  array   $original
	 * @param  array   $assets
	 * @return bool
	 */
	private function dependency_is_valid($asset, $dependency, $original, $assets)
	{
		if ( ! isset($original[$dependency]))
		{
			return false;
		}
		elseif ($dependency === $asset)
		{
			throw new \Exception("Asset [$asset] is dependent on itself.");
		}
		elseif (isset($assets[$dependency]) and in_array($asset, $assets[$dependency]['dependencies']))
		{
			throw new \Exception("Assets [$asset] and [$dependency] have a circular dependency.");
		}
	}

}