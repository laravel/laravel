<?php

class RouteTest extends PHPUnit_Framework_TestCase {

	/**
	 * Destroy the testing environment.
	 */
	public function tearDown()
	{
		Request::$route = null;
	}

	/**
	 * Tests the Route::handles method.
	 *
	 * @group laravel
	 */
	public function testHandlesIndicatesIfTheRouteHandlesAGivenURI()
	{
		$route = new Laravel\Routing\Route('GET /', array('handles' => array('GET /foo/bar')));

		$this->assertTrue($route->handles('foo/*'));
		$this->assertTrue($route->handles('foo/bar'));
		$this->assertFalse($route->handles('baz'));

		$route = new Laravel\Routing\Route('GET /', array('handles' => array('GET /', 'GET /home')));

		$this->assertTrue($route->handles('/'));
		$this->assertTrue($route->handles('home'));
		$this->assertFalse($route->handles('foo'));
	}

}