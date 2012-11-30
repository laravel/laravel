<?php

use Laravel\Fluent;

class FluentTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test the Fluent constructor.
	 *
	 * @group laravel
	 */
	public function testAttributesAreSetByConstructor()
	{
		$array = array('name' => 'Taylor', 'age' => 25);

		$fluent = new Fluent($array);

		$this->assertEquals($array, $fluent->attributes);
	}

	/**
	 * Test the Fluent::get method.
	 *
	 * @group laravel
	 */
	public function testGetMethodReturnsAttribute()
	{
		$fluent = new Fluent(array('name' => 'Taylor'));

		$this->assertEquals('Taylor', $fluent->get('name'));
		$this->assertEquals('Default', $fluent->get('foo', 'Default'));
		$this->assertEquals('Taylor', $fluent->name);
		$this->assertNull($fluent->foo);
	}

	public function testMagicMethodsCanBeUsedToSetAttributes()
	{
		$fluent = new Fluent;

		$fluent->name = 'Taylor';
		$fluent->developer();
		$fluent->age(25);

		$this->assertEquals('Taylor', $fluent->name);
		$this->assertTrue($fluent->developer);
		$this->assertEquals(25, $fluent->age);
		$this->assertInstanceOf('Laravel\\Fluent', $fluent->programmer());
	}

}