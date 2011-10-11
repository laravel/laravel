<?php

class RequestTest extends PHPUnit_Framework_TestCase {

	public function setUp()
	{
		$_SERVER = array();
		Laravel\Request::$uri = null;
	}

	/**
	 * @expectedException Exception
	 */
	public function test_exception_thrown_if_uri_cant_be_determined()
	{
		Laravel\Request::uri();
	}

	public function test_uri_method_returns_path_info_if_set()
	{
		$_SERVER['PATH_INFO'] = 'something';
		$this->assertEquals('something', Laravel\Request::uri());
	}

	/**
	 * @dataProvider requestUriProvider
	 */
	public function test_correct_uri_is_returned_when_request_uri_is_used($uri, $expectation)
	{
		$_SERVER['REQUEST_URI'] = $uri;

		$this->assertEquals($expectation, Laravel\Request::uri());
	}

	public function requestUriProvider()
	{
		return array(
			array('/index.php', '/'),
			array('/index.php/', '/'),
			array('http://localhost/user', 'user'),
			array('http://localhost/user/', 'user'),
			array('http://localhost/index.php', '/'),
			array('http://localhost/index.php/', '/'),
			array('http://localhost/index.php//', '/'),
			array('http://localhost/index.php/user', 'user'),
			array('http://localhost/index.php/user/', 'user'),
			array('http://localhost/index.php/user/profile', 'user/profile'),
		);
	}

}