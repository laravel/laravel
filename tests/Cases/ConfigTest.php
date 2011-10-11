<?php namespace Laravel; use PHPUnit_Framework_TestCase;

class ConfigTest extends PHPUnit_Framework_TestCase {

	public function test_has_method_indicates_if_configuration_item_exists()
	{
		Config::set('hasvalue', true);
		$this->assertTrue(Config::has('hasvalue'));
	}

	public function test_has_method_returns_false_when_item_doesnt_exist()
	{
		$this->assertFalse(Config::has('something'));
	}

	public function test_config_get_can_retrieve_item_from_configuration()
	{
		$this->assertTrue(is_array(Config::get('application')));
		$this->assertEquals(Config::get('application.url'), 'http://localhost');
	}

	public function test_get_method_returns_default_when_requested_item_doesnt_exist()
	{
		$this->assertNull(Config::get('config.item'));
		$this->assertEquals(Config::get('config.item', 'test'), 'test');
		$this->assertEquals(Config::get('config.item', function() {return 'test';}), 'test');
	}

	public function test_config_set_can_set_configuration_items()
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