<?php

class RouteTest extends PHPUnit_Framework_TestCase {

	public function testSimpleRouteCallbackReturnsResponseInstance()
	{
		$route = new System\Route('GET /', function() {return 'test';});

		$this->assertInstanceOf('System\\Response', $route->call());
		$this->assertEquals($route->call()->content, 'test');
	}

	public function testRouteCallPassesParametersToCallback()
	{
		$route = new System\Route('GET /', function($parameter) {return $parameter;}, array('test'));
		$this->assertEquals($route->call()->content, 'test');

		$route = new System\Route('GET /', function($parameter1, $parameter2) {return $parameter1.$parameter2;}, array('test1', 'test2'));
		$this->assertEquals($route->call()->content, 'test1test2');
	}

	public function testRouteCallWithNullBeforeFilterReturnsRouteResponse()
	{
		$route = new System\Route('GET /', array('before' => 'test', 'do' => function() {return 'route';}));
		System\Route\Filter::$filters = array('test' => function() {return null;});

		$this->assertEquals($route->call()->content, 'route');
	}

	public function testRouteCallWithOverridingBeforeFilterReturnsFilterResponse()
	{
		$route = new System\Route('GET /', array('before' => 'test', 'do' => function() {return 'route';}));
		System\Route\Filter::$filters = array('test' => function() {return 'filter';});

		$this->assertEquals($route->call()->content, 'filter');
	}

	public function testRouteAfterFilterIsCalled()
	{
		$route = new System\Route('GET /', array('after' => 'test', 'do' => function() {return 'route';}));
		System\Route\Filter::$filters = array('test' => function() {define('LARAVEL_TEST_AFTER_FILTER', 'ran');});

		$route->call();

		$this->assertTrue(defined('LARAVEL_TEST_AFTER_FILTER'));
	}

	public function testRouteAfterFilterDoesNotAffectResponse()
	{
		$route = new System\Route('GET /', array('after' => 'test', 'do' => function() {return 'route';}));
		System\Route\Filter::$filters = array('test' => function() {return 'filter';});

		$this->assertEquals($route->call()->content, 'route');
	}

}