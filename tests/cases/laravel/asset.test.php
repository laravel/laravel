<?php

class AssetTest extends PHPUnit_Framework_TestCase {

	/**
	 * Initialize the test environment.
	 */
	public function setUp()
	{
		Config::$items = array();
		Config::$cache = array();
		Asset::$containers = array();
	}

	/**
	 * Test the Asset::container method.
	 *
	 * @group laravel
	 */
	public function testContainersCanBeCreated()
	{
		$container = Asset::container('foo');

		$this->assertTrue($container === Asset::container('foo'));
		$this->assertInstanceOf('\\Laravel\\Asset_Container', $container);
	}

	/**
	 * Test the Asset::container method for default container creation.
	 *
	 * @group laravel
	 */
	public function testDefaultContainerCreatedByDefault()
	{
		$this->assertEquals('default', Asset::container()->name);
	}

	/**
	 * Test the Asset::__callStatic method.
	 *
	 * @group laravel
	 */
	public function testContainerMethodsCanBeDynamicallyCalled()
	{
		Asset::style('common', 'common.css');

		$this->assertEquals('common.css', Asset::container()->assets['style']['common']['source']);
	}

	/**
	 * Test the Asset_Container constructor.
	 *
	 * @group laravel
	 */
	public function testNameIsSetOnAssetContainerConstruction()
	{
		$container = $this->getContainer();

		$this->assertEquals('foo', $container->name);
	}

	/**
	 * Test the Asset_Container::add method.
	 *
	 * @group laravel
	 */
	public function testAddMethodProperlySniffsAssetType()
	{
		$container = $this->getContainer();

		$container->add('jquery', 'jquery.js');
		$container->add('common', 'common.css');

		$this->assertEquals('jquery.js', $container->assets['script']['jquery']['source']);
		$this->assertEquals('common.css', $container->assets['style']['common']['source']);
	}

	/**
	 * Test the Asset_Container::style method.
	 *
	 * @group laravel
	 */
	public function testStyleMethodProperlyRegistersAnAsset()
	{
		$container = $this->getContainer();

		$container->style('common', 'common.css');

		$this->assertEquals('common.css', $container->assets['style']['common']['source']);
	}

	/**
	 * Test the Asset_Container::style method sets media attribute.
	 *
	 * @group laravel
	 */
	public function testStyleMethodProperlySetsMediaAttributeIfNotSet()
	{
		$container = $this->getContainer();

		$container->style('common', 'common.css');

		$this->assertEquals('all', $container->assets['style']['common']['attributes']['media']);
	}

	/**
	 * Test the Asset_Container::style method sets media attribute.
	 *
	 * @group laravel
	 */
	public function testStyleMethodProperlyIgnoresMediaAttributeIfSet()
	{
		$container = $this->getContainer();

		$container->style('common', 'common.css', array(), array('media' => 'print'));

		$this->assertEquals('print', $container->assets['style']['common']['attributes']['media']);
	}

	/**
	 * Test the Asset_Container::script method.
	 *
	 * @group laravel
	 */
	public function testScriptMethodProperlyRegistersAnAsset()
	{
		$container = $this->getContainer();

		$container->script('jquery', 'jquery.js');

		$this->assertEquals('jquery.js', $container->assets['script']['jquery']['source']);
	}

	/**
	 * Test the Asset_Container::add method properly sets dependencies.
	 *
	 * @group laravel
	 */
	public function testAddMethodProperlySetsDependencies()
	{
		$container = $this->getContainer();

		$container->add('common', 'common.css', 'jquery');
		$container->add('jquery', 'jquery.js', array('jquery-ui'));

		$this->assertEquals(array('jquery'), $container->assets['style']['common']['dependencies']);
		$this->assertEquals(array('jquery-ui'), $container->assets['script']['jquery']['dependencies']);
	}

	/**
	 * Test the Asset_Container::add method properly sets attributes.
	 *
	 * @group laravel
	 */
	public function testAddMethodProperlySetsAttributes()
	{
		$container = $this->getContainer();

		$container->add('common', 'common.css', array(), array('media' => 'print'));
		$container->add('jquery', 'jquery.js', array(), array('defer'));

		$this->assertEquals(array('media' => 'print'), $container->assets['style']['common']['attributes']);
		$this->assertEquals(array('defer'), $container->assets['script']['jquery']['attributes']);
	}

	/**
	 * Test the Asset_Container::bundle method.
	 *
	 * @group laravel
	 */
	public function testBundleMethodCorrectlySetsTheAssetBundle()
	{
		$container = $this->getContainer();

		$container->bundle('eloquent');

		$this->assertEquals('eloquent', $container->bundle);
	}

	/**
	 * Test the Asset_Container::path method.
	 *
	 * @group laravel
	 */
	public function testPathMethodReturnsCorrectPathForABundleAsset()
	{
		Config::$cache['application.url'] = 'http://localhost';

		$container = $this->getContainer();

		$container->bundle('eloquent');

		$this->assertEquals('http://localhost/bundles/eloquent/foo.jpg', $container->path('foo.jpg'));
	}

	/**
	 * Test the Asset_Container::path method.
	 *
	 * @group laravel
	 */
	public function testPathMethodReturnsCorrectPathForAnApplicationAsset()
	{
		Config::$cache['application.url'] = 'http://localhost';

		$container = $this->getContainer();

		$this->assertEquals('http://localhost/foo.jpg', $container->path('foo.jpg'));
	}

	/**
	 * Test the Asset_Container::scripts method.
	 *
	 * @group laravel
	 */
	public function testScriptsCanBeRetrieved()
	{
		$container = $this->getContainer();

		$container->script('dojo', 'dojo.js', array('jquery-ui'));
		$container->script('jquery', 'jquery.js', array('jquery-ui', 'dojo'));
		$container->script('jquery-ui', 'jquery-ui.js');

		$scripts = $container->scripts();

		$this->assertTrue(strpos($scripts, 'jquery.js') > 0);
		$this->assertTrue(strpos($scripts, 'jquery.js') > strpos($scripts, 'jquery-ui.js'));
		$this->assertTrue(strpos($scripts, 'dojo.js') > strpos($scripts, 'jquery-ui.js'));
	}

	/**
	 * Test the Asset_Container::styles method.
	 *
	 * @group laravel
	 */
	public function testStylesCanBeRetrieved()
	{
		$container = $this->getContainer();

		$container->style('dojo', 'dojo.css', array('jquery-ui'), array('media' => 'print'));
		$container->style('jquery', 'jquery.css', array('jquery-ui', 'dojo'));
		$container->style('jquery-ui', 'jquery-ui.css');

		$styles = $container->styles();

		$this->assertTrue(strpos($styles, 'jquery.css') > 0);
		$this->assertTrue(strpos($styles, 'media="print"') > 0);
		$this->assertTrue(strpos($styles, 'jquery.css') > strpos($styles, 'jquery-ui.css'));
		$this->assertTrue(strpos($styles, 'dojo.css') > strpos($styles, 'jquery-ui.css'));
	}

	/**
	 * Get an asset container instance.
	 *
	 * @param  string           $name
	 * @return Asset_Container
	 */
	private function getContainer($name = 'foo')
	{
		return new Laravel\Asset_Container($name);
	}

}