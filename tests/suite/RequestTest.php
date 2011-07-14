<?php

class RequestTest extends PHPUnit_Framework_TestCase {

	public function setUp()
	{
		unset($_SERVER['PATH_INFO'], $_SERVER['REQUEST_METHOD']);

		$route = new System\Route(null, null);
		$route->callback = array('name' => 'test', 'do' => function() {});

		System\Request::$route = $route;
	}

	public function tearDown()
	{
		System\Request::$route = null;
	}

	/**
	 * @expectedException Exception
	 */
	public function testUriMethodThrowsExceptionWhenCantDetermineUri()
	{
		System\Request::uri();
	}

	public function testUriMethodReturnsPathInfoWhenSet()
	{
		$_SERVER['PATH_INFO'] = 'test';
		$_SERVER['REQUEST_METHOD'] = 'blah';

		$this->assertEquals(System\Request::uri(), 'test');
	}

	/**
	 * @dataProvider rootUriProvider1
	 */
	public function testUriMethodReturnsSingleSlashOnRequestForRoot($uri)
	{
		Config::set('application.url', 'http://example.com');
		Config::set('appliation.index', '');

		$_SERVER['REQUEST_URI'] = $uri;
		$this->assertEquals(System\Request::uri(), '/');
	}

	public function rootUriProvider1()
	{
		return array(
			array(''),
			array('/'),
			array('/index.php'),
			array('/index.php/'),
			array('/index.php///'),
			array('http://example.com'),
			array('http://example.com/'),
		);
	}

	/**
	 * @dataProvider rootUriProvider2
	 */
	public function testUriMethodReturnsSingleSlashOnRequestForFolderNestedRoot($uri)
	{
		Config::set('application.url', 'http://example.com/laravel/public');
		Config::set('appliation.index', 'index.php');

		$_SERVER['REQUEST_URI'] = $uri;
		$this->assertEquals(System\Request::uri(), '/');
	}

	public function rootUriProvider2()
	{
		return array(
			array('http://example.com/laravel/public'),
			array('http://example.com/laravel/public/index.php'),
			array('http://example.com/laravel/public/index.php/'),
			array('http://example.com/laravel/public/index.php///'),
			array(''),
			array('/'),
			array('/index.php'),
			array('/index.php/'),
			array('/index.php///'),
			array('http://example.com'),
			array('http://example.com/'),
		);
	}	

	/**
	 * @dataProvider segmentedUriProvider1
	 */
	public function testUriMethodReturnsSegmentForSingleSegmentUri($uri)
	{
		Config::set('application.url', 'http://example.com');
		Config::set('appliation.index', '');

		$_SERVER['REQUEST_URI'] = $uri;
		$this->assertEquals(System\Request::uri(), 'user');
	}

	public function segmentedUriProvider1()
	{
		return array(
			array('http://example.com/user'),
			array('http://example.com/user/'),
			array('http://example.com/user//'),
		);
	}

	/**
	 * @dataProvider segmentedUriProvider2
	 */
	public function testUriMethodReturnsSegmentsForMultiSegmentUri($uri)
	{
		Config::set('application.url', 'http://example.com');
		Config::set('appliation.index', '');

		$_SERVER['REQUEST_URI'] = $uri;
		$this->assertEquals(System\Request::uri(), 'user/something');
	}

	public function segmentedUriProvider2()
	{
		return array(
			array('http://example.com/user/something'),
			array('http://example.com/user/something/'),
			array('http://example.com/user/something//'),
		);
	}

	public function testMethodForNonSpoofedRequests()
	{
		$_SERVER['REQUEST_METHOD'] = 'GET';
		$this->assertEquals(System\Request::method(), 'GET');
	}

	public function testMethodForSpoofedRequests()
	{
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$_POST['REQUEST_METHOD'] = 'PUT';
		$this->assertEquals(System\Request::method(), 'PUT');

		$_POST['REQUEST_METHOD'] = 'DELETE';
		$this->assertEquals(System\Request::method(), 'DELETE');
	}

	public function testRouteIsReturnsFalseWhenNoSuchNamedRouteExists()
	{
		$route = new System\Route(null, null);
		$route->callback = function() {};

		System\Request::$route = $route;

		$this->assertFalse(System\Request::route_is('test'));
		$this->assertFalse(System\Request::route_is_test());
	}

	public function testRouteIsReturnsFalseWhenWrongRouteNameIsGiven()
	{
		$this->assertFalse(System\Request::route_is('something'));
		$this->assertFalse(System\Request::route_is_something());
	}

	public function testRouteIsReturnsTrueWhenNamedRouteExists()
	{
		$this->assertTrue(System\Request::route_is('test'));
		$this->assertTrue(System\Request::route_is_test());
	}

}