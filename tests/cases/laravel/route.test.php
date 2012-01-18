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
	public function testHandlesReturnsTrueWhenRouteHandlesTheGivenURI()
	{
		$route = new Laravel\Routing\Route('GET /', array('handles' => array('foo/bar')));

		$this->assertTrue($route->handles('foo/*'));
		$this->assertTrue($route->handles('foo/bar'));
	}

}