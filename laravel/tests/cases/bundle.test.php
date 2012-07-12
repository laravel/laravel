<?php

class BundleTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Bundle::$started = array();
		Bundle::$elements = array();
		unset(Bundle::$bundles['foo']);
	}

	/**
	 * Tear down the test environment.
	 */
	public function tearDown()
	{
		Bundle::$started = array();
		Bundle::$elements = array();
		unset(Bundle::$bundles['foo']);
	}

	/**
	 * Test Bundle::register method.
	 *
	 * @group laravel
	 */
	public function testRegisterMethodCorrectlyRegistersBundle()
	{
		Bundle::register('foo-baz', array('handles' => 'foo-baz'));
		$this->assertEquals('foo-baz', Bundle::$bundles['foo-baz']['handles']);
		$this->assertFalse(Bundle::$bundles['foo-baz']['auto']);

		Bundle::register('foo-bar', array());
		$this->assertFalse(Bundle::$bundles['foo-baz']['auto']);
		$this->assertNull(Bundle::$bundles['foo-bar']['handles']);

		unset(Bundle::$bundles['foo-baz']);
		unset(Bundle::$bundles['foo-bar']);
	}

	/**
	 * Test the Bundle::start method.
	 *
	 * @group laravel
	 */
	public function testStartMethodStartsBundle()
	{
		$_SERVER['bundle.dummy.start'] = 0;
		$_SERVER['bundle.dummy.routes'] = 0;

		$_SERVER['started.dummy'] = false;

		Event::listen('laravel.started: dummy', function()
		{
			$_SERVER['started.dummy'] = true;
		});

		Bundle::register('dummy');
		Bundle::start('dummy');

		$this->assertTrue($_SERVER['started.dummy']);
		$this->assertEquals(1, $_SERVER['bundle.dummy.start']);
		$this->assertEquals(1, $_SERVER['bundle.dummy.routes']);

		Bundle::start('dummy');

		$this->assertEquals(1, $_SERVER['bundle.dummy.start']);
		$this->assertEquals(1, $_SERVER['bundle.dummy.routes']);
	}

	/**
	 * Test Bundle::handles method.
	 *
	 * @group laravel
	 */
	public function testHandlesMethodReturnsBundleThatHandlesURI()
	{
		Bundle::register('foo', array('handles' => 'foo-bar'));
		$this->assertEquals('foo', Bundle::handles('foo-bar/admin'));
		unset(Bundle::$bundles['foo']);
	}

	/**
	 * Test the Bundle::exist method.
	 *
	 * @group laravel
	 */
	public function testExistMethodIndicatesIfBundleExist()
	{
		$this->assertTrue(Bundle::exists('dashboard'));
		$this->assertFalse(Bundle::exists('foo'));
	}

	/**
	 * Test the Bundle::started method.
	 *
	 * @group laravel
	 */
	public function testStartedMethodIndicatesIfBundleIsStarted()
	{
		Bundle::register('dummy');
		Bundle::start('dummy');
		$this->assertTrue(Bundle::started('dummy'));
	}

	/**
	 * Test the Bundle::prefix method.
	 *
	 * @group laravel
	 */
	public function testPrefixMethodReturnsCorrectPrefix()
	{
		$this->assertEquals('dummy::', Bundle::prefix('dummy'));
		$this->assertEquals('', Bundle::prefix(DEFAULT_BUNDLE));
	}

	/**
	 * Test the Bundle::class_prefix method.
	 *
	 * @group laravel
	 */
	public function testClassPrefixMethodReturnsProperClassPrefixForBundle()
	{
		$this->assertEquals('Dummy_', Bundle::class_prefix('dummy'));
		$this->assertEquals('', Bundle::class_prefix(DEFAULT_BUNDLE));
	}

	/**
	 * Test the Bundle::path method.
	 *
	 * @group laravel
	 */
	public function testPathMethodReturnsCorrectPath()
	{
		$this->assertEquals(path('app'), Bundle::path(null));
		$this->assertEquals(path('app'), Bundle::path(DEFAULT_BUNDLE));
		$this->assertEquals(path('bundle').'dashboard'.DS, Bundle::path('dashboard'));
	}

	/**
	 * Test the Bundle::asset method.
	 *
	 * @group laravel
	 */
	public function testAssetPathReturnsPathToBundlesAssets()
	{
		Config::set('application.url', 'http://localhost');

		$this->assertEquals('http://localhost/bundles/dashboard/', Bundle::assets('dashboard'));
		$this->assertEquals('http://localhost/', Bundle::assets(DEFAULT_BUNDLE));

		Config::set('application.url', '');
	}

	/**
	 * Test the Bundle::name method.
	 *
	 * @group laravel
	 */
	public function testBundleNameCanBeRetrievedFromIdentifier()
	{
		$this->assertEquals(DEFAULT_BUNDLE, Bundle::name('something'));
		$this->assertEquals(DEFAULT_BUNDLE, Bundle::name('something.else'));
		$this->assertEquals('bundle', Bundle::name('bundle::something.else'));
	}

	/**
	 * Test the Bundle::element method.
	 *
	 * @group laravel
	 */
	public function testElementCanBeRetrievedFromIdentifier()
	{
		$this->assertEquals('something', Bundle::element('something'));
		$this->assertEquals('something.else', Bundle::element('something.else'));
		$this->assertEquals('something.else', Bundle::element('bundle::something.else'));
	}

	/**
	 * Test the Bundle::identifier method.
	 *
	 * @group laravel
	 */
	public function testIdentifierCanBeConstructed()
	{
		$this->assertEquals('something.else', Bundle::identifier(DEFAULT_BUNDLE, 'something.else'));
		$this->assertEquals('dashboard::something', Bundle::identifier('dashboard', 'something'));
		$this->assertEquals('dashboard::something.else', Bundle::identifier('dashboard', 'something.else'));
	}

	/**
	 * Test the Bundle::resolve method.
	 *
	 * @group laravel
	 */
	public function testBundleNamesCanBeResolved()
	{
		$this->assertEquals(DEFAULT_BUNDLE, Bundle::resolve('foo'));
		$this->assertEquals('dashboard', Bundle::resolve('dashboard'));
	}

	/**
	 * Test the Bundle::parse method.
	 *
	 * @group laravel
	 */
	public function testParseMethodReturnsElementAndIdentifier()
	{
		$this->assertEquals(array('application', 'something'), Bundle::parse('something'));
		$this->assertEquals(array('application', 'something.else'), Bundle::parse('something.else'));
		$this->assertEquals(array('dashboard', 'something'), Bundle::parse('dashboard::something'));
		$this->assertEquals(array('dashboard', 'something.else'), Bundle::parse('dashboard::something.else'));
	}

	/**
	 * Test the Bundle::get method.
	 *
	 * @group laravel
	 */
	public function testOptionMethodReturnsBundleOption()
	{
		$this->assertFalse(Bundle::option('dashboard', 'auto'));
		$this->assertEquals('dashboard', Bundle::option('dashboard', 'location'));
	}

	/**
	 * Test the Bundle::all method.
	 *
	 * @group laravel
	 */
	public function testAllMethodReturnsBundleArray()
	{
		Bundle::register('foo');
		$this->assertEquals(Bundle::$bundles, Bundle::all());
		unset(Bundle::$bundles['foo']);
	}

	/**
	 * Test the Bundle::names method.
	 *
	 * @group laravel
	 */
	public function testNamesMethodReturnsBundleNames()
	{
		Bundle::register('foo');
		$this->assertEquals(array('dashboard', 'dummy', 'foo'), Bundle::names());
		unset(Bundle::$bundles['foo']);
	}

}