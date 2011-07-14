<?php

class RoutingTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass()
	{
		$routes = array();

		$routes['GET /'] = function() {return 'root';};
		$routes['GET /home'] = array('name' => 'home', 'do' => function() {});
		$routes['POST /home'] = array('name' => 'post-home', 'do' => function() {});
		$routes['GET /user/(:num)'] = array('name' => 'user', 'do' => function() {});
		$routes['GET /user/(:any)/(:num)/edit'] = array('name' => 'edit', 'do' => function() {});

		System\Router::$routes = $routes;
	}

	public function testRouterReturnsNullWhenNotFound()
	{
		$this->assertNull(System\Router::route('GET', 'not-found'));
	}

	public function testRouterRoutesToProperRouteWhenSegmentsArePresent()
	{
		$this->assertEquals(System\Router::route('GET', 'home')->callback['name'], 'home');
		$this->assertEquals(System\Router::route('POST', 'home')->callback['name'], 'post-home');
		$this->assertEquals(System\Router::route('GET', 'user/1')->callback['name'], 'user');
		$this->assertEquals(System\Router::route('GET', 'user/taylor/25/edit')->callback['name'], 'edit');
	}

	public function testRouterReturnsNullWhenRouteNotFound()
	{
		$this->assertNull(System\Router::route('POST', 'user/taylor/25/edit'));
		$this->assertNull(System\Router::route('GET', 'user/taylor/taylor/edit'));
		$this->assertNull(System\Router::route('GET', 'user/taylor'));
		$this->assertNull(System\Router::route('GET', 'user/12-3'));
	}

	public static function tearDownAfterClass()
	{
		System\Router::$routes = null;
	}

}