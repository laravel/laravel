<?php

class RouteIsTest extends PHPUnit_Framework_TestCase {

	public function setUp()
	{
		$route = new System\Route(null, null);
		$route->callback = array('name' => 'test', 'do' => function() {});

		System\Request::$route = $route;
	}

	public function tearDown()
	{
		System\Request::$route = null;
	}

	public function testRouteIsReturnsFalseWhenNoName()
	{
		$route = new System\Route(null, null);
		$route->callback = function() {};

		System\Request::$route = $route;

		$this->assertFalse(System\Request::route_is('test'));
		$this->assertFalse(System\Request::route_is_test());
	}

	public function testRouteIsReturnsFalseWhenWrongName()
	{
		$this->assertFalse(System\Request::route_is('something'));
		$this->assertFalse(System\Request::route_is_something());
	}

	public function testRouteIsReturnsTrueWhenMatch()
	{
		$this->assertTrue(System\Request::route_is('test'));
		$this->assertTrue(System\Request::route_is_test());
	}

}