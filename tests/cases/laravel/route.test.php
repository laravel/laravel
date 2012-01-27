<?php

use Laravel\Routing\Route;

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
		$route = new Route('GET /', array('handles' => array('GET /foo/bar')));

		$this->assertTrue($route->handles('foo/*'));
		$this->assertTrue($route->handles('foo/bar'));
		$this->assertFalse($route->handles('/'));
		$this->assertFalse($route->handles('baz'));
		$this->assertFalse($route->handles('/foo'));
		$this->assertFalse($route->handles('foo'));

		$route = new Route('GET /', array('handles' => array('GET /', 'GET /home')));

		$this->assertTrue($route->handles('/'));
		$this->assertTrue($route->handles('home'));
		$this->assertFalse($route->handles('foo'));
	}

	/**
	 * Tests the Route::is method.
	 *
	 * @group laravel
	 */
	public function testIsMethodIndicatesIfTheRouteHasAGivenName()
	{
		$route = new Route('GET /', array('name' => 'profile'));

		$this->assertTrue($route->is('profile'));
		$this->assertFalse($route->is('something'));
	}

}