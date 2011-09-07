<?php namespace Laravel;

class Asset {

	/**
	 * All of the instantiated asset containers.
	 *
	 * Asset containers are created through the container method, and are singletons.
	 *
	 * @var array
	 */
	public $containers = array();

	/**
	 * The HTML writer instance.
	 *
	 * @var HTML
	 */
	protected $html;

	/**
	 * Create a new asset manager instance.
	 *
	 * @param  HTML  $html
	 * @return void
	 */
	public function __construct(HTML $html)
	{
		$this->html = $html;
	}

	/**
	 * Get an asset container instance.
	 *
	 * If no container name is specified, the default container will be returned.
	 * Containers provide a convenient method of grouping assets while maintaining
	 * expressive code and a clean API.
	 *
	 * @param  string            $container
	 * @return Asset_Container
	 */
	public function container($container = 'default')
	{
		if ( ! isset($this->containers[$container]))
		{
			$this->containers[$container] = new Asset_Container($container, $this->html);
		}

		return $this->containers[$container];
	}

	/**
	 * Magic Method for calling methods on the default Asset container.
	 *
	 * This provides a convenient API, allowing the develop to skip the "container"
	 * method when using the default container.
	 */
	public function __call($method, $parameters)
	{
		return call_user_func_array(array($this->container(), $method), $parameters);
	}

}

class Asset_Container {

	/**
	 * The asset container name.
	 *
	 * This name may be used to access the container instance via the Asset::container method.
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
	 * The HTML writer instance.
	 *
	 * @var HTML
	 */
	protected $html;

	/**
	 * Create a new asset container instance.
	 *
	 * @param  string  $name
	 * @param  HTML    $html
	 * @return void
	 */
	public function __construct($name, HTML $html)
	{
		$this->name = $name;
		$this->html = $html;
	}

	/**
	 * Add an asset to the container.
	 *
	 * The extension of the asset source will be used to determine the type of
	 * asset being registered (CSS or JavaScript). If you are using a non-standard
	 * extension, you may use the style or script methods to register assets.
	 *
	 * You may also specify asset dependencies. This will instruct the class to
	 * only link to the registered asset after its dependencies have been linked.
	 * For example, you may wish to make jQuery UI dependent on jQuery.
	 *
	 * @param  string  $name
	 * @param  string  $source
	 * @param  array   $dependencies
	 * @param  array   $attributes
	 * @return void
	 */
	public function add($name, $source, $dependencies = array(), $attributes = array())
	{
		$type = (pathinfo($source, PATHINFO_EXTENSION) == 'css') ? 'style' : 'script';

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
	 */
	public function script($name, $source, $dependencies = array(), $attributes = array())
	{
		$this->register('script', $name, $source, $dependencies, $attributes);
	}

	/**
	 * Add an asset to the array of registered assets.
	 *
	 * Assets are organized in the array by type (CSS or JavaScript).
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
	 * Get all of the registered assets for a given type / group.
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
	 * @param  string  $name
	 * @return string
	 */
	public function get_script($name)
	{
		return $this->get_asset('script', $name);
	}

	/**
	 * Get the HTML link to a registered asset.
	 *
	 * @param  string  $group
	 * @param  string  $name
	 * @return string
	 */
	private function get_asset($group, $name)
	{
		if ( ! isset($this->assets[$group][$name])) return '';

		$asset = $this->assets[$group][$name];

		return $this->html->$group($asset['source'], $asset['attributes']);
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
	 * Verify that an asset's dependency is valid.
	 *
	 * A dependency is considered valid if it exists, is not a circular reference, and is
	 * not a reference to the owning asset itself.
	 *
	 * @param  string  $asset
	 * @param  string  $dependency
	 * @param  array   $original
	 * @param  array   $assets
	 * @return bool
	 */
	private function dependency_is_valid($asset, $dependency, $original, $assets)
	{
		if ( ! isset($original[$dependency])) return false;

		if ($dependency === $asset)
		{
			throw new \Exception("Asset [$asset] is dependent on itself.");
		}
		elseif (isset($assets[$dependency]) and in_array($asset, $assets[$dependency]['dependencies']))
		{
			throw new \Exception("Assets [$asset] and [$dependency] have a circular dependency.");
		}
	}

}