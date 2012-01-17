<?php

class StrTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test the Str::encoding method.
	 *
	 * @group laravel
	 */
	public function testEncodingShouldReturnApplicationEncoding()
	{
		$this->assertEquals('UTF-8', Config::get('application.encoding'));
		Config::set('application.encoding', 'foo');
		$this->assertEquals('foo', Config::get('application.encoding'));
		Config::set('application.encoding', 'UTF-8');
	}

}