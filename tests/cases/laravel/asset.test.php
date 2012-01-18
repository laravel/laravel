<?php

class AssetTest extends PHPUnit_Framework_TestCase {

	/**
	 * Initialize the test environment.
	 */
	public function setUp()
	{
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
		$container = new Laravel\Asset_Container('foo');

		$this->assertEquals('foo', $container->name);
	}

	/**
	 * Test the Asset_Container::add method.
	 *
	 * @group laravel
	 */
	public function testAddMethodProperlySniffsAssetType()
	{
		$container = new Laravel\Asset_Container('foo');

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
		$container = new Laravel\Asset_Container('foo');

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
		$container = new Laravel\Asset_Container('foo');

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
		$container = new Laravel\Asset_Container('foo');

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
		$container = new Laravel\Asset_Container('foo');

		$container->script('jquery', 'jquery.js');

		$this->assertEquals('jquery.js', $container->assets['script']['jquery']['source']);
	}

}