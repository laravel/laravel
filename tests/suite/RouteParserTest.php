<?php

class RouteParserTest extends PHPUnit_Framework_TestCase {

	public function testParserReturnsNoParametersWhenNoneArePresent()
	{
		$this->assertEmpty(System\Router::parameters('/test/route', '/test/route'));
		$this->assertEmpty(System\Router::parameters('/', '/'));
	}

	public function testParserReturnsParametersWhenTheyArePresent()
	{
		$this->assertEquals(System\Router::parameters('/user/1', '/user/(:num)'), array(1));
		$this->assertEquals(System\Router::parameters('/user/1/2', '/user/(:num)/(:num)'), array(1, 2));
		$this->assertEquals(System\Router::parameters('/user/1/test', '/user/(:num)/(:any)'), array(1, 'test'));
		$this->assertEquals(System\Router::parameters('/user/1/test/again', '/user/(:num)/test/(:any)'), array(1, 'again'));
	}

}