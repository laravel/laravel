<?php

use Symfony\Component\HttpFoundation\LaravelRequest as RequestFoundation;

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
	 * Set this request's URI to the given string
	 * 
	 * @param string  $uri
	 */
	protected function setRequestUri($uri)
	{
		// FIXME: Ugly hack, but old contents from previous requests seem to
		// trip up the Foundation class.
		$_FILES = array();
		
		$_SERVER['REQUEST_URI'] = $uri;
		Request::$foundation = RequestFoundation::createFromGlobals();
	}

	/**
	 * Test the URI::current method.
	 *
	 * @group laravel
	 * @dataProvider requestUriProvider
	 */
	public function testCorrectURIIsReturnedByCurrentMethod($uri, $expectation)
	{
		$this->setRequestUri($uri);

		$this->assertEquals($expectation, URI::current());
	}

	/**
	 * Test the URI::segment method.
	 *
	 * @group laravel
	 */
	public function testSegmentMethodReturnsAURISegment()
	{
		$this->setRequestUri('/user/profile');

		$this->assertEquals('user', URI::segment(1));
		$this->assertEquals('profile', URI::segment(2));
	}

	/**
	 * Data provider for the URI::current test.
	 */
	public function requestUriProvider()
	{
		return array(
			array('/user', 'user'),
			array('/user/', 'user'),
			array('', '/'),
			array('/', '/'),
			array('//', '/'),
			array('/user', 'user'),
			array('/user/', 'user'),
			array('/user/profile', 'user/profile'),
		);
	}

}