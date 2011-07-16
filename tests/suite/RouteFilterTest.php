<?php

class RouteFilerTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass()
	{
		$filters = array(
			'test' => function() {return 'test';},
			'vars' => function($var) {return $var;},
			'vars2' => function($var1, $var2) {return $var1.$var2;},
		);

		System\Route\Filter::$filters = $filters;
	}

	public static function tearDownAfterClass()
	{
		System\Route\Filter::$filters = require APP_PATH.'filters'.EXT;
	}

	/**
	 * @expectedException Exception
	 */
	public function testCallingUndefinedFilterThrowsException()
	{
		System\Route\Filter::call('not-found');
	}

	public function testCallingFilterWithoutOverrideReturnsNull()
	{
		$this->assertNull(System\Route\Filter::call('test'));
	}

	public function testCallingFilterWithOverrideReturnsResult()
	{
		$this->assertEquals(System\Route\Filter::call('test', array(), true), 'test');
	}

	public function testCallingFilterWithParametersPassesParametersToFilter()
	{
		$this->assertEquals(System\Route\Filter::call('vars', array('test'), true), 'test');
		$this->assertEquals(System\Route\Filter::call('vars2', array('test1', 'test2'), true), 'test1test2');
	}

}