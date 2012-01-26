<?php

class ConfigTest extends PHPUnit_Framework_TestCase {

	/**
	 * Tear down the testing environment.
	 */
	public function tearDown()
	{
		Config::$items = array();
		Config::$cache = array();
	}

	/**
	 * Test the Config::get method.
	 *
	 * @group laravel
	 */
	public function testItemsCanBeRetrievedFromConfigFiles()
	{
		$this->assertEquals('UTF-8', Config::get('application.encoding'));
		$this->assertEquals('mysql', Config::get('database.connections.mysql.driver'));
		$this->assertEquals('dashboard', Config::get('dashboard::meta.bundle'));
	}

	/**
	 * Test the Config::has method.
	 *
	 * @group laravel
	 */
	public function testHasMethodIndicatesIfConfigItemExists()
	{
		$this->assertFalse(Config::has('application.foo'));
		$this->assertTrue(Config::has('application.encoding'));
	}

	/**
	 * Test the Config::set method.
	 *
	 * @group laravel
	 */
	public function testConfigItemsCanBeSet()
	{
		Config::set('application.encoding', 'foo');
		Config::set('dashboard::meta.bundle', 'bar');

		$this->assertEquals('foo', Config::get('application.encoding'));
		$this->assertEquals('bar', Config::get('dashboard::meta.bundle'));
	}

	/**
	 * Test that environment configurations are loaded correctly.
	 *
	 * @group laravel
	 */
	public function testEnvironmentConfigsOverrideNormalConfigurations()
	{
		$_SERVER['LARAVEL_ENV'] = 'local';

		$this->assertEquals('sqlite', Config::get('database.default'));

		unset($_SERVER['LARAVEL_ENV']);
	}

}