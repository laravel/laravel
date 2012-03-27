<?php

class IoCTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test IoC::register and IoC::resolve.
	 *
	 * @group laravel
	 */
	public function testRegisteredClassCanBeResolved()
	{
		IoC::register('foo', function()
		{
			return 'Taylor';
		});

		$this->assertEquals('Taylor', IoC::resolve('foo'));
	}

	/**
	 * Test that singletons are created once.
	 *
	 * @group laravel
	 */
	public function testSingletonsAreCreatedOnce()
	{
		IoC::singleton('foo', function()
		{
			return new StdClass;
		});

		$object = IoC::resolve('foo');

		$this->assertTrue($object === IoC::resolve('foo'));
	}

	/**
	 * Test the IoC::instance method.
	 *
	 * @group laravel
	 */
	public function testInstancesAreReturnedBySingleton()
	{
		$object = new StdClass;

		IoC::instance('bar', $object);

		$this->assertTrue($object === IoC::resolve('bar'));
	}

	/**
	 * Test the IoC::registered method.
	 */
	public function testRegisteredMethodIndicatesIfRegistered()
	{
		IoC::register('foo', function() {});

		$this->assertTrue(IoC::registered('foo'));
		$this->assertFalse(IoC::registered('baz'));
	}

	/**
	 * Test the IoC::controller method.
	 *
	 * @group laravel
	 */
	public function testControllerMethodRegistersAController()
	{
		IoC::controller('ioc.test', function() {});

		$this->assertTrue(IoC::registered('controller: ioc.test'));
	}

	/**
	 * Test the IoC::core method.
	 *
	 * @group laravel
	 */
	public function testCoreMethodReturnsFromLaravel()
	{
		IoC::register('laravel.ioc.test', function() { return 'Taylor'; });

		$this->assertEquals('Taylor', IoC::core('ioc.test'));
	}

}