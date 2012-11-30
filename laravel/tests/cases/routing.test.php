<?php

use Laravel\Routing\Router;

class RoutingTest extends PHPUnit_Framework_TestCase {

	/**
	 * Destroy the testing environment.
	 */
	public function setUp()
	{
		Bundle::$started = array();
		Bundle::$routed = array();
		Router::$names = array();
		Router::$routes = array();
	}

	/**
	 * Destroy the testing environment.
	 */
	public function tearDown()
	{
		Bundle::$started = array();
		Bundle::$routed = array();
		Router::$names = array();
		Router::$routes = array();
	}

	/**
	 * Test the Router::find method.
	 *
	 * @group laravel
	 */
	public function testNamedRoutesCanBeLocatedByTheRouter()
	{
		Route::get('/', array('as' => 'home'));
		Route::get('dashboard', array('as' => 'dashboard'));

		$home = Router::find('home');
		$dashboard = Router::find('dashboard');

		$this->assertTrue(isset($home['/']));
		$this->assertTrue(isset($dashboard['dashboard']));
	}

	/**
	 * Test the basic routing mechanism.
	 *
	 * @group laravel
	 */
	public function testBasicRouteCanBeRouted()
	{
		Route::get('/', function() {});
		Route::get('home, main', function() {});

		$this->assertEquals('/', Router::route('GET', '/')->uri);
		$this->assertEquals('home', Router::route('GET', 'home')->uri);
		$this->assertEquals('main', Router::route('GET', 'main')->uri);
	}

	/**
	 * Test that the router can handle basic wildcards.
	 *
	 * @group laravel
	 */
	public function testWildcardRoutesCanBeRouted()
	{
		Route::get('user/(:num)', function() {});
		Route::get('profile/(:any)/(:num)', function() {});

		$this->assertNull(Router::route('GET', 'user/1.5'));
		$this->assertNull(Router::route('GET', 'user/taylor'));
		$this->assertEquals(array(25), Router::route('GET', 'user/25')->parameters);
		$this->assertEquals('user/(:num)', Router::route('GET', 'user/1')->uri);

		$this->assertNull(Router::route('GET', 'profile/1/otwell'));
		$this->assertNull(Router::route('POST', 'profile/taylor/1'));
		$this->assertNull(Router::route('GET', 'profile/taylor/otwell'));
		$this->assertNull(Router::route('GET', 'profile/taylor/1/otwell'));
		$this->assertEquals(array('taylor', 25), Router::route('GET', 'profile/taylor/25')->parameters);
		$this->assertEquals('profile/(:any)/(:num)', Router::route('GET', 'profile/taylor/1')->uri);
	}

	/**
	 * Test that optional wildcards can be routed.
	 *
	 * @group laravel
	 */
	public function testOptionalWildcardsCanBeRouted()
	{
		Route::get('user/(:num?)', function() {});
		Route::get('profile/(:any)/(:any?)', function() {});

		$this->assertNull(Router::route('GET', 'user/taylor'));
		$this->assertEquals('user/(:num?)', Router::route('GET', 'user')->uri);
		$this->assertEquals(array(25), Router::route('GET', 'user/25')->parameters);
		$this->assertEquals('user/(:num?)', Router::route('GET', 'user/1')->uri);

		$this->assertNull(Router::route('GET', 'profile/taylor/otwell/test'));
		$this->assertEquals('profile/(:any)/(:any?)', Router::route('GET', 'profile/taylor')->uri);
		$this->assertEquals('profile/(:any)/(:any?)', Router::route('GET', 'profile/taylor/25')->uri);
		$this->assertEquals('profile/(:any)/(:any?)', Router::route('GET', 'profile/taylor/otwell')->uri);
		$this->assertEquals(array('taylor', 'otwell'), Router::route('GET', 'profile/taylor/otwell')->parameters);
	}

	/**
	 * Test that basic controller routing is working.
	 *
	 * @group laravel
	 */
	public function testBasicRouteToControllerIsRouted()
	{
		$this->assertEquals('auth@(:1)', Router::route('GET', 'auth')->action['uses']);
		$this->assertEquals('home@(:1)', Router::route('GET', 'home/index')->action['uses']);
		$this->assertEquals('home@(:1)', Router::route('GET', 'home/profile')->action['uses']);
		$this->assertEquals('admin.panel@(:1)', Router::route('GET', 'admin/panel')->action['uses']);
		$this->assertEquals('admin.panel@(:1)', Router::route('GET', 'admin/panel/show')->action['uses']);
	}

	/**
	 * Test basic bundle route resolution.
	 *
	 * @group laravel
	 */
	public function testRoutesToBundlesCanBeResolved()
	{
		$this->assertNull(Router::route('GET', 'dashboard/foo'));
		$this->assertEquals('dashboard', Router::route('GET', 'dashboard')->uri);
	}

	/**
	 * Test bundle controller route resolution.
	 *
	 * @group laravel
	 */
	public function testBundleControllersCanBeResolved()
	{
		$this->assertEquals('dashboard::panel@(:1)', Router::route('GET', 'dashboard/panel')->action['uses']);
		$this->assertEquals('dashboard::panel@(:1)', Router::route('GET', 'dashboard/panel/show')->action['uses']);
	}

	/**
	 * Test foreign characters can be used in routes.
	 *
	 * @group laravel
	 */
	public function testForeignCharsInRoutes()
	{
		Route::get(urlencode('مدرس_رياضيات').'/(:any)', function() {});
		Route::get(urlencode('مدرس_رياضيات'), function() {});
		Route::get(urlencode('ÇœŪ'), function() {});
		Route::get(urlencode('私は料理が大好き'), function() {});

		$this->assertEquals(array(urlencode('مدرس_رياضيات')), Router::route('GET', urlencode('مدرس_رياضيات').'/'.urlencode('مدرس_رياضيات'))->parameters);
		$this->assertEquals(urlencode('مدرس_رياضيات'), Router::route('GET', urlencode('مدرس_رياضيات'))->uri);
		$this->assertEquals(urlencode('ÇœŪ'), Router::route('GET', urlencode('ÇœŪ'))->uri);
		$this->assertEquals(urlencode('私は料理が大好き'), Router::route('GET', urlencode('私は料理が大好き'))->uri);
	}

}