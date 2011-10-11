<?php

class RequestTest extends PHPUnit_Framework_TestCase {

	public function test_uri_method_returns_path_info_if_set()
	{
		$_SERVER['PATH_INFO'] = 'something';
		$this->assertEquals('something', Laravel\Request::uri());
	}

}