<?php

class AssetTest extends PHPUnit_Framework_TestCase {

	public function testContainerMethodReturnsContainer()
	{
		$asset = Laravel\IoC::resolve('laravel.asset');

		$this->assertInstanceOf('Laravel\\Asset_Container', $asset->container());
		$this->assertInstanceOf('Laravel\\Asset_Container', $asset->container('footer'));

		$this->assertEquals($asset->container()->name, 'default');
		$this->assertEquals($asset->container('footer')->name, 'footer');
	}

	public function testAssetManagerMagicallyCallsDefaultContainer()
	{
		$asset = Laravel\IoC::resolve('laravel.asset');

		$mock = $this->getMockBuilder('Laravel\\Asset_Container')->disableOriginalConstructor()->getMock();

		$mock->expects($this->any())->method('styles')->will($this->returnValue('styles'));
		
		$asset->containers['default'] = $mock;

		$this->assertEquals($asset->styles(), 'styles');
	}

	public function testAddMethodAddsAssetBasedOnExtension()
	{
		$container = $this->getContainer();

		$container->add('jquery', 'js/jquery.js');
		$container->add('jquery-css', 'css/jquery.css');

		$this->assertEquals($container->assets['script']['jquery']['source'], 'js/jquery.js');
		$this->assertEquals($container->assets['style']['jquery-css']['source'], 'css/jquery.css');
	}

	/**
	 * @dataProvider assetProvider
	 */
	public function testStyleMethodRegistersStylesheetAsset($type, $source, $attributes, $testAttributes)
	{
		$container = $this->getContainer();

		$dependencies = array('jquery');

		$container->$type('reset', $source, $dependencies, $attributes);

		$this->assertEquals($container->assets[$type]['reset']['source'], $source);
		$this->assertEquals($container->assets[$type]['reset']['dependencies'], $dependencies);
		$this->assertEquals($container->assets[$type]['reset']['attributes'], $testAttributes);
	}

	public function assetProvider()
	{
		$attributes = array('test' => 'test');

		return array(
			array('style', 'css/reset.css', $attributes, array_merge($attributes, array('media' => 'all'))),
			array('script', 'js/jquery.js', $attributes, $attributes),
		);
	}

	public function testAllStylesCanBeRetrievedViaStylesMethod()
	{
		$container = new Laravel\Asset_Container('default', new HTMLAssetStub);

		$container->style('reset', 'css/reset.css');
		$container->style('jquery', 'css/jquery.css');

		$this->assertEquals($container->styles(), 'css/reset.css media:allcss/jquery.css media:all');
	}

	public function testAllScriptsCanBeRetrievedViaScriptsMethod()
	{
		$container = new Laravel\Asset_Container('default', new HTMLAssetStub);

		$container->script('jquery-ui', 'js/jquery-ui.js');
		$container->script('jquery', 'js/jquery.js', array(), array('test' => 'value'));

		$this->assertEquals($container->scripts(), 'js/jquery-ui.js js/jquery.js test:value');
	}

	public function testAssetsAreSortedBasedOnDependencies()
	{
		$container = $this->getContainer();

		$container->script('jquery', 'js/jquery.js', array('jquery-ui'));
		$container->script('jquery-ui', 'js/jquery-ui.js');

		$scripts = $container->scripts();

		$this->assertTrue(strpos($scripts, 'js/jquery-ui.js') < strpos($scripts, 'js/jquery.js'));
	}

	/**
	 * @expectedException Exception
	 */
	public function testAssetsCannotBeDependentOnSelf()
	{
		$container = $this->getContainer();

		$container->script('jquery', 'js/jquery.js', array('jquery'));

		$container->scripts();
	}

	/**
	 * @expectedException Exception
	 */
	public function testAssetDependenciesCannotBeCircular()
	{
		$container = $this->getContainer();

		$container->script('jquery', 'js/jquery.js', array('jquery-ui'));
		$container->script('jquery-ui', 'js/jquery-ui.js', array('jquery'));
		
		$container->scripts();
	}

	private function getContainer()
	{
		return new Laravel\Asset_Container('default', Laravel\IoC::resolve('laravel.html'));
	}

}

class HTMLAssetStub extends Laravel\HTML {

	public function __construct() {}

	public function style($source, $attributes)
	{
		return $source.' '.$this->getAttributes($attributes);
	}

	public function script($source, $attributes)
	{
		return $source.' '.$this->getAttributes($attributes);
	}

	private function getAttributes($attributes)
	{
		$html = '';

		foreach ($attributes as $key => $value)
		{
			$html .= $key.':'.$value;
		}

		return $html;
	}

}