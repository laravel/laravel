<?php

class EloquentTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test the Model constructor.
	 *
	 * @group laravel
	 */
	public function testAttributesAreSetByConstructor()
	{
		$array = array('name' => 'Taylor', 'age' => 25, 'setter' => 'foo');

		$model = new Model($array);

		$this->assertEquals('Taylor', $model->name);
		$this->assertEquals(25, $model->age);
		$this->assertEquals('setter: foo', $model->setter);
	}

	/**
	 * Test the Model::fill method.
	 *
	 * @group laravel
	 */
	public function testAttributesAreSetByFillMethod()
	{
		$array = array('name' => 'Taylor', 'age' => 25, 'setter' => 'foo');

		$model = new Model();
		$model->fill($array);

		$this->assertEquals('Taylor', $model->name);
		$this->assertEquals(25, $model->age);
		$this->assertEquals('setter: foo', $model->setter);
	}

	/**
	 * Test the Model::fill_raw method.
	 *
	 * @group laravel
	 */
	public function testAttributesAreSetByFillRawMethod()
	{
		$array = array('name' => 'Taylor', 'age' => 25, 'setter' => 'foo');

		$model = new Model();
		$model->fill_raw($array);

		$this->assertEquals($array, $model->attributes);
	}

	/**
	 * Test the Model::fill method with accessible.
	 *
	 * @group laravel
	 */
	public function testAttributesAreSetByFillMethodWithAccessible()
	{
		Model::$accessible = array('name', 'age');

		$array = array('name' => 'Taylor', 'age' => 25, 'foo' => 'bar');

		$model = new Model();
		$model->fill($array);

		$this->assertEquals('Taylor', $model->name);
		$this->assertEquals(25, $model->age);
		$this->assertNull($model->foo);

		Model::$accessible = null;
	}

	/**
	 * Test the Model::fill method with empty accessible array.
	 *
	 * @group laravel
	 */
	public function testAttributesAreSetByFillMethodWithEmptyAccessible()
	{
		Model::$accessible = array();

		$array = array('name' => 'Taylor', 'age' => 25, 'foo' => 'bar');

		$model = new Model();
		$model->fill($array);

		$this->assertEquals(array(), $model->attributes);
		$this->assertNull($model->name);
		$this->assertNull($model->age);
		$this->assertNull($model->foo);

		Model::$accessible = null;
	}

	/**
	 * Test the Model::fill_raw method with accessible.
	 *
	 * @group laravel
	 */
	public function testAttributesAreSetByFillRawMethodWithAccessible()
	{
		Model::$accessible = array('name', 'age');

		$array = array('name' => 'taylor', 'age' => 25, 'setter' => 'foo');

		$model = new Model();
		$model->fill_raw($array);

		$this->assertEquals($array, $model->attributes);

		Model::$accessible = null;
	}

	/**
	 * Test the Model::__set method.
	 *
	 * @group laravel
	 */
	public function testAttributeMagicSetterMethodChangesAttribute()
	{
		Model::$accessible = array('setter');

		$array = array('setter' => 'foo', 'getter' => 'bar');

		$model = new Model($array);
		$model->setter = 'bar';
		$model->getter = 'foo';

		$this->assertEquals('setter: bar', $model->get_attribute('setter'));
		$this->assertEquals('foo', $model->get_attribute('getter'));

		Model::$accessible = null;
	}

	/**
	 * Test the Model::__get method.
	 *
	 * @group laravel
	 */
	public function testAttributeMagicGetterMethodReturnsAttribute()
	{
		$array = array('setter' => 'foo', 'getter' => 'bar');

		$model = new Model($array);

		$this->assertEquals('setter: foo', $model->setter);
		$this->assertEquals('getter: bar', $model->getter);
	}

	/**
	 * Test the Model::set_* method.
	 *
	 * @group laravel
	 */
	public function testAttributeSetterMethodChangesAttribute()
	{
		Model::$accessible = array('setter');

		$array = array('setter' => 'foo', 'getter' => 'bar');

		$model = new Model($array);
		$model->set_setter('bar');
		$model->set_getter('foo');

		$this->assertEquals('setter: bar', $model->get_attribute('setter'));
		$this->assertEquals('foo', $model->get_attribute('getter'));

		Model::$accessible = null;
	}

	/**
	 * Test the Model::get_* method.
	 *
	 * @group laravel
	 */
	public function testAttributeGetterMethodReturnsAttribute()
	{
		$array = array('setter' => 'foo', 'getter' => 'bar');

		$model = new Model($array);

		$this->assertEquals('setter: foo', $model->get_setter());
		$this->assertEquals('getter: bar', $model->get_getter());
	}

	/**
	 * Test determination of dirty/changed attributes.
	 *
	 * @group laravel
	 */
	public function testDeterminationOfChangedAttributes()
	{
		$array = array('name' => 'Taylor', 'age' => 25, 'foo' => null);

		$model = new Model($array, true);
		$model->name = 'Otwell';
		$model->new = null;

		$this->assertTrue($model->changed('name'));
		$this->assertFalse($model->changed('age'));
		$this->assertFalse($model->changed('foo'));
		$this->assertFalse($model->changed('new'));
		$this->assertTrue($model->dirty());
		$this->assertEquals(array('name' => 'Otwell', 'new' => null), $model->get_dirty());

		$model->sync();

		$this->assertFalse($model->changed('name'));
		$this->assertFalse($model->changed('age'));
		$this->assertFalse($model->changed('foo'));
		$this->assertFalse($model->changed('new'));
		$this->assertFalse($model->dirty());
		$this->assertEquals(array(), $model->get_dirty());
	}

	/**
	 * Test the Model::purge method.
	 *
	 * @group laravel
	 */
	public function testAttributePurge()
	{
		$array = array('name' => 'Taylor', 'age' => 25);

		$model = new Model($array);
		$model->name = 'Otwell';
		$model->age = 26;

		$model->purge('name');

		$this->assertFalse($model->changed('name'));
		$this->assertNull($model->name);
		$this->assertTrue($model->changed('age'));
		$this->assertEquals(26, $model->age);
		$this->assertEquals(array('age' => 26), $model->get_dirty());
	}

	/**
	 * Test the Model::table method.
	 *
	 * @group laravel
	 */
	public function testTableMethodReturnsCorrectName()
	{
		$model = new Model();
		$this->assertEquals('models', $model->table());

		Model::$table = 'table';
		$this->assertEquals('table', $model->table());

		Model::$table = null;
		$this->assertEquals('models', $model->table());
	}

	/**
	 * Test the Model::to_array method.
	 *
	 * @group laravel
	 */
	public function testConvertingToArray()
	{
		Model::$hidden = array('password', 'hidden');

		$array = array('name' => 'Taylor', 'age' => 25, 'password' => 'laravel', 'null' => null);

		$model = new Model($array);

		$first = new Model(array('first' => 'foo', 'password' => 'hidden'));
		$second = new Model(array('second' => 'bar', 'password' => 'hidden'));
		$third = new Model(array('third' => 'baz', 'password' => 'hidden'));

		$model->relationships['one'] = new Model(array('foo' => 'bar', 'password' => 'hidden'));
		$model->relationships['many'] = array($first, $second, $third);
		$model->relationships['hidden'] = new Model(array('should' => 'not_visible'));
		$model->relationships['null'] = null;

		$this->assertEquals(array(
			'name' => 'Taylor', 'age' => 25, 'null' => null,
			'one' => array('foo' => 'bar'),
			'many' => array(
				array('first' => 'foo'),
				array('second' => 'bar'),
				array('third' => 'baz'),
			),
			'null' => null,
		), $model->to_array());

	}

}