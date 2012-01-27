<?php

use Laravel\Routing\Router;

class RoutingTest extends PHPUnit_Framework_TestCase {

	/**
	 * Destroy the testing environment.
	 */
	public function tearDown()
	{
		Router::$names = array();
		Router::$routes = array();
	}

	/**
	 * Test the basic routing mechanism.
	 *
	 * @group laravel
	 */
	public function testBasicRouteCanBeRouted()
	{
		Router::register('GET /', function() {});
		Router::register('GET /home', function() {});

		$this->assertEquals('GET /', Router::route('GET', '/')->key);
		$this->assertEquals('GET /home', Router::route('GET', '/home')->key);
	}

	/**
	 * Test that the router can handle basic wildcards.
	 *
	 * @group laravel
	 */
	public function testWildcardRoutesCanBeRouted()
	{
		Router::register('GET /user/(:num)', function() {});
		Router::register('GET /profile/(:any)/(:num)', function() {});

		$this->assertNull(Router::route('GET', 'user/1.5'));
		$this->assertNull(Router::route('GET', 'user/taylor'));
		$this->assertEquals('GET /user/(:num)', Router::route('GET', 'user/1')->key);

		$this->assertNull(Router::route('GET', 'profile/1/otwell'));
		$this->assertNull(Router::route('POST', 'profile/taylor/1'));
		$this->assertNull(Router::route('GET', 'profile/taylor/otwell'));
		$this->assertNull(Router::route('GET', 'profile/taylor/1/otwell'));
		$this->assertEquals('GET /profile/(:any)/(:num)', Router::route('GET', 'profile/taylor/1')->key);
	}

	/**
	 * Test that optional wildcards can be routed.
	 *
	 * @group laravel
	 */
	public function testOptionalWildcardsCanBeRouted()
	{
		Router::register('GET /user/(:num?)', function() {});
		Router::register('GET /profile/(:any)/(:any?)', function() {});

		$this->assertNull(Router::route('GET', 'user/taylor'));
		$this->assertEquals('GET /user/(:num?)', Router::route('GET', 'user')->key);
		$this->assertEquals('GET /user/(:num?)', Router::route('GET', 'user/1')->key);

		$this->assertNull(Router::route('GET', 'profile/taylor/otwell/test'));
		$this->assertEquals('GET /profile/(:any)/(:any?)', Router::route('GET', 'profile/taylor')->key);
		$this->assertEquals('GET /profile/(:any)/(:any?)', Router::route('GET', 'profile/taylor/25')->key);
		$this->assertEquals('GET /profile/(:any)/(:any?)', Router::route('GET', 'profile/taylor/otwell')->key);
	}

}