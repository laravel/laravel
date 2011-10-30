<?php

class RequestTest extends PHPUnit_Framework_TestCase {

	public function setUp()
	{
		$_POST = array();
		$_SERVER = array();

		Laravel\Request::$uri = null;
	}

	/**
	 * @dataProvider requestUriProvider
	 */
	public function test_correct_uri_is_returned_when_request_uri_is_used($uri, $expectation)
	{
		$_SERVER['REQUEST_URI'] = $uri;
		$this->assertEquals($expectation, Laravel\Request::uri());
	}

	public function test_request_method_returns_spoofed_method_if_uri_is_spoofed()
	{
		$_POST = array(Laravel\Request::spoofer => 'something');
		$this->assertEquals('something', Laravel\Request::method());
	}

	public function test_request_method_returns_request_method_from_server_array()
	{
		$_SERVER['REQUEST_METHOD'] = 'PUT';
		$this->assertEquals('PUT', Laravel\Request::method());
	}

	public function test_server_method_returns_from_the_server_array()
	{
		$_SERVER = array('TEST' => 'something', 'USER' => array('NAME' => 'taylor'));
		$this->assertEquals('something', Laravel\Request::server('test'));
		$this->assertEquals('taylor', Laravel\Request::server('user.name'));
	}

	public function test_spoofed_returns_true_when_request_is_spoofed()
	{
		$_POST[Laravel\Request::spoofer] = 'something';
		$this->assertTrue(Laravel\Request::spoofed());
	}

	public function test_spoofed_returns_false_when_request_isnt_spoofed()
	{
		$this->assertFalse(Laravel\Request::spoofed());
	}

	public function test_ip_method_returns_client_ip_address()
	{
		$_SERVER['REMOTE_ADDR'] = 'something';
		$this->assertEquals('something', Laravel\Request::ip());

		$_SERVER['HTTP_CLIENT_IP'] = 'something';
		$this->assertEquals('something', Laravel\Request::ip());

		$_SERVER['HTTP_X_FORWARDED_FOR'] = 'something';
		$this->assertEquals('something', Laravel\Request::ip());

		$_SERVER = array();
		$this->assertEquals('0.0.0.0', Laravel\Request::ip());
	}

	public function test_protocol_returns_server_protocol()
	{
		$_SERVER['SERVER_PROTOCOL'] = 'taylor';
		$this->assertEquals('taylor', Laravel\Request::protocol());

		unset($_SERVER['SERVER_PROTOCOL']);
		$this->assertEquals('HTTP/1.1', Laravel\Request::protocol());
	}

	public function test_ajax_method_returns_false_when_not_ajax()
	{
		$this->assertFalse(Laravel\Request::ajax());
	}

	public function test_ajax_method_returns_true_when_ajax()
	{
		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
		$this->assertTrue(Laravel\Request::ajax());
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