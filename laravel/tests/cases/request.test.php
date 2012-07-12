<?php

class SessionPayloadTokenStub {

	public function token() { return 'Taylor'; }

}

class RequestTest extends PHPUnit_Framework_TestCase {

	/**
	 * Tear down the test environment.
	 */
	public function tearDown()
	{
		$_POST = array();
		$_SERVER = array();
		Request::$route = null;
		Session::$instance = null;
	}

	/**
	 * Test the Request::method method.
	 *
	 * @group laravel
	 */
	public function testMethodReturnsTheHTTPRequestMethod()
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';

		$this->assertEquals('POST', Request::method());

		$_POST[Request::spoofer] = 'PUT';

		$this->assertEquals('PUT', Request::method());
	}

	/**
	 * Test the Request::server method.
	 *
	 * @group laravel
	 */
	public function testServerMethodReturnsFromServerArray()
	{
		$_SERVER = array('TEST' => 'something', 'USER' => array('NAME' => 'taylor'));

		$this->assertEquals('something', Request::server('test'));
		$this->assertEquals('taylor', Request::server('user.name'));
	}

	/**
	 * Test the Request::ip method.
	 *
	 * @group laravel
	 */
	public function testIPMethodReturnsClientIPAddress()
	{
		$_SERVER['REMOTE_ADDR'] = 'something';
		$this->assertEquals('something', Request::ip());

		$_SERVER['HTTP_CLIENT_IP'] = 'something';
		$this->assertEquals('something', Request::ip());

		$_SERVER['HTTP_X_FORWARDED_FOR'] = 'something';
		$this->assertEquals('something', Request::ip());

		$_SERVER = array();
		$this->assertEquals('0.0.0.0', Request::ip());
	}

	/**
	 * Test the Request::protocol method.
	 *
	 * @group laravel
	 */
	public function testProtocolMethodReturnsProtocol()
	{
		$_SERVER['SERVER_PROTOCOL'] = 'taylor';
		$this->assertEquals('taylor', Request::protocol());

		unset($_SERVER['SERVER_PROTOCOL']);
		$this->assertEquals('HTTP/1.1', Request::protocol());
	}

	/**
	 * Test the Request::secure method.
	 *
	 * @group laravel
	 */
	public function testSecureMethodsIndicatesIfHTTPS()
	{
		$_SERVER['HTTPS'] = 'on';

		$this->assertTrue(Request::secure());

		$_SERVER['HTTPS'] = 'off';

		$this->assertFalse(Request::secure());
	}

	/**
	 * Test the Request::ajax method.
	 *
	 * @group laravel
	 */
	public function testAjaxMethodIndicatesWhenAjax()
	{
		$this->assertFalse(Request::ajax());

		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';

		$this->assertTrue(Request::ajax());
	}

	/**
	 * Test the Request::forged method.
	 *
	 * @group laravel
	 */
	public function testForgedMethodIndicatesIfRequestWasForged()
	{
		Session::$instance = new SessionPayloadTokenStub;

		$input = array(Session::csrf_token => 'Foo');
		Request::foundation()->request->add($input);

		$this->assertTrue(Request::forged());

		$input = array(Session::csrf_token => 'Taylor');
		Request::foundation()->request->add($input);
		
		$this->assertFalse(Request::forged());
	}

	/**
	 * Test the Request::route method.
	 *
	 * @group laravel
	 */
	public function testRouteMethodReturnsStaticRoute()
	{
		Request::$route = 'Taylor';

		$this->assertEquals('Taylor', Request::route());
	}

}