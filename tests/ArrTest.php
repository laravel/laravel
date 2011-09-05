<?php

class ArrTest extends PHPUnit_Framework_TestCase {


	/**
	 * @dataProvider getArray
	 */
	public function testGetMethodReturnsItemsFromArray($array)
	{
		$this->assertEquals(Arr::get($array, 'email'), $array['email']);
		$this->assertEquals(Arr::get($array, 'names.uncle'), $array['names']['uncle']);
	}


	/**
	 * @dataProvider getArray
	 */
	public function testGetMethodReturnsDefaultWhenItemDoesntExist($array)
	{
		$this->assertNull(Arr::get($array, 'names.aunt'));
		$this->assertEquals(Arr::get($array, 'names.aunt', 'Tammy'), 'Tammy');
		$this->assertEquals(Arr::get($array, 'names.aunt', function() {return 'Tammy';}), 'Tammy');
	}


	/**
	 * @dataProvider getArray
	 */
	public function testSetMethodSetsItemsInArray($array)
	{
		Arr::set($array, 'name', 'Taylor');
		Arr::set($array, 'names.aunt', 'Tammy');
		Arr::set($array, 'names.friends.best', 'Abigail');

		$this->assertEquals($array['name'], 'Taylor');
		$this->assertEquals($array['names']['aunt'], 'Tammy');
		$this->assertEquals($array['names']['friends']['best'], 'Abigail');

	}


	public function getArray()
	{
		return array(array(
			array(
				'email' => 'taylorotwell@gmail.com',
				'names' => array(
					'uncle' => 'Mike',
				),
			)
		));
	}

}