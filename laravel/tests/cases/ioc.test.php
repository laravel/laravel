<?php

/**
 * Testing Optional Parameters in classes' Dependency Injection
 */
class TestOptionalParamClassForIoC
{
	public function __construct($optional_param = 42) {}
}

/**
 * Testing Dependency Injection with this class
 */
class TestClassOneForIoC
{
	public $_variable;
}

/**
 * Testing Dependency Injection of ClassOne
 */
class TestClassTwoForIoC
{
	public $class_one;
	public function __construct(TestClassOneForIoC $class_one)
	{
		$this->class_one = $class_one;
	}
}

use \Laravel\IoC as IoC;

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
		IoC::register('controller: ioc.test', function() {});

		$this->assertTrue(IoC::registered('controller: ioc.test'));
	}

	/**
	 * Test that classes with optional parameters can resolve
	 */
	public function testOptionalParamClassResolves()
	{
		$test = IoC::resolve('TestOptionalParamClassForIoC');
		$this->assertInstanceOf('TestOptionalParamClassForIoC', $test);
	}

	/**
	 * Test that we can resolve TestClassOneForIoC using IoC
	 */
	public function testClassOneForIoCResolves()
	{
		$test = IoC::resolve('TestClassOneForIoC');
		$this->assertInstanceOf('TestClassOneForIoC', $test);
	}

	/**
	 * Test that we can resolve TestClassTwoForIoC
	 */
	public function testClassTwoForIoCResolves()
	{
		$test = IoC::resolve('TestClassTwoForIoC');
		$this->assertInstanceOf('TestClassTwoForIoC', $test);
	}

	/**
	 * Test that when we resolve TestClassTwoForIoC we auto resolve
	 * the dependency for TestClassOneForIoC
	 */
	public function testClassTwoResolvesClassOneDependency()
	{
		$test = IoC::resolve('TestClassTwoForIoC');
		$this->assertInstanceOf('TestClassOneForIoC', $test->class_one);
	}

	/**
	 * Test that when we resolve TestClassTwoForIoC with a parameter
	 * that it actually uses that instead of a blank class TestClassOneForIoC
	 */
	public function testClassTwoResolvesClassOneWithArgument()
	{
		$class_one = IoC::resolve('TestClassOneForIoC');
		$class_one->test_variable = 42;

		$class_two = IoC::resolve('TestClassTwoForIoC', array($class_one));
		$this->assertEquals(42, $class_two->class_one->test_variable);
	}

    public function testCanUnregisterRegistered()
    {
        $testClass = 'test';

        IoC::register($testClass, function() {});

        $this->assertTrue(IoC::registered($testClass));

        IoC::unregister($testClass);

        $this->assertFalse(IoC::registered($testClass));
    }

}
