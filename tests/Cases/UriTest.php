<?php

class UriTest extends PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider uri_provider
	 */
	public function test_get_method_returns_correct_uri($uri, $expectation)
	{
		$uri = new Laravel\URI(array('REQUEST_URI' => $uri));

		$this->assertEquals($uri->get(), $expectation);
	}

	public function uri_provider()
	{
		return array(
			array('/index.php/', '/'),
			array('/index.php//', '/'),
			array('', '/'),
			array('/', '/'),
			array('/index.php/user', 'user'),
			array('/index.php/user/something', 'user/something'),
			array('/user', 'user'),
			array('/user/something', 'user/something'),
			array('http://localhost/index.php/user', 'user'),
			array('http://localhost/user', 'user'),
			array('http://localhost/index.php/', '/'),
		);
	}

}