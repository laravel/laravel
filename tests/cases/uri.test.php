<?php

class URITest extends PHPUnit_Framework_TestCase {

	/**
	 * Destroy the test environment.
	 */
	public function tearDown()
	{
		$_SERVER = array();
		URI::$uri = null;
		URI::$segments = array();
	}

	/**
	 * Test the URI::current method.
	 *
	 * @group laravel
	 * @dataProvider requestUriProvider
	 */
	public function testCorrectURIIsReturnedByCurrentMethod($uri, $expectation)
	{
		$_SERVER['REQUEST_URI'] = $uri;
		$this->assertEquals($expectation, URI::current());
	}

	/**
	 * Test the URI::segment method.
	 *
	 * @group laravel
	 */
	public function testSegmentMethodReturnsAURISegment()
	{
		$_SERVER['REQUEST_URI'] = 'http://localhost/index.php/user/profile';

		$this->assertEquals('user', URI::segment(1));
		$this->assertEquals('profile', URI::segment(2));
	}

	/**
	 * Data provider for the URI::current test.
	 */
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