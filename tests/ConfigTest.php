<?php

class ConfigTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass()
	{
		IoC::container()->singletons = array();
	}

	public function tearDown()
	{
		IoC::container()->singletons = array();
	}

	/**
	 * @dataProvider getGetMocker
	 */
	public function testHasMethodReturnsTrueWhenItemExists($mock, $mocker)
	{
		$mocker->will($this->returnValue('value'));

		$this->assertTrue($mock->has('something'));
	}

	/**
	 * @dataProvider getGetMocker
	 */
	public function testHasMethodReturnsFalseWhenItemDoesntExist($mock, $mocker)
	{
		$mocker->will($this->returnValue(null));

		$this->assertFalse($mock->has('something'));
	}

	public function getGetMocker()
	{
		$mock = $this->getMock('Laravel\\Config', array('get'), array(null));

		return array(array($mock, $mock->expects($this->any())->method('get')));
	}

	public function testConfigClassCanRetrieveItems()
	{
		$config = IoC::container()->config;

		$this->assertTrue(is_array($config->get('application')));
		$this->assertEquals($config->get('application.url'), 'http://localhost');
	}

	public function testGetMethodReturnsDefaultWhenItemDoesntExist()
	{
		$config = IoC::container()->config;

		$this->assertNull($config->get('config.item'));
		$this->assertEquals($config->get('config.item', 'test'), 'test');
		$this->assertEquals($config->get('config.item', function() {return 'test';}), 'test');
	}

	public function testConfigClassCanSetItems()
	{
		$config = IoC::container()->config;

		$config->set('application.names.test', 'test');
		$config->set('application.url', 'test');
		$config->set('session', array());
		$config->set('test', array());

		$this->assertEquals($config->get('application.names.test'), 'test');
		$this->assertEquals($config->get('application.url'), 'test');
		$this->assertEquals($config->get('session'), array());
		$this->assertEquals($config->get('test'), array());
	}

}