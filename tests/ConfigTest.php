<?php

class ConfigTest extends PHPUnit_Framework_TestCase {

	public function testHasMethodReturnsTrueWhenItemExists()
	{
		Config::set('hasvalue', true);
		
		$this->assertTrue(Config::has('hasvalue'));
	}

	public function testHasMethodReturnsFalseWhenItemDoesntExist()
	{
		$this->assertFalse(Config::has('something'));
	}

	public function testConfigClassCanRetrieveItems()
	{
		$this->assertTrue(is_array(Config::get('application')));
		$this->assertEquals(Config::get('application.url'), 'http://localhost');
	}

	public function testGetMethodReturnsDefaultWhenItemDoesntExist()
	{
		$this->assertNull(Config::get('config.item'));
		$this->assertEquals(Config::get('config.item', 'test'), 'test');
		$this->assertEquals(Config::get('config.item', function() {return 'test';}), 'test');
	}

	public function testConfigClassCanSetItems()
	{
		Config::set('application.names.test', 'test');
		Config::set('application.url', 'test');
		Config::set('session', array());
		Config::set('test', array());

		$this->assertEquals(Config::get('application.names.test'), 'test');
		$this->assertEquals(Config::get('application.url'), 'test');
		$this->assertEquals(Config::get('session'), array());
		$this->assertEquals(Config::get('test'), array());
	}

}