<?php

class ArrTest extends PHPUnit_Framework_TestCase {

	public function testReturnsDefaultWhenItemNotPresentInArray()
	{
		$this->assertNull(System\Arr::get(array(), 'name'));
		$this->assertEquals(System\Arr::get(array(), 'name', 'test'), 'test');
		$this->assertEquals(System\Arr::get(array(), 'name', function() {return 'test';}), 'test');
	}

	public function testReturnsItemWhenPresentInArray()
	{
		$this->assertEquals(System\Arr::get(array('name' => 'test'), 'name'), 'test');
	}

}