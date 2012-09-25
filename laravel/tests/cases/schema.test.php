<?php

class SchemaTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test the "exists" method.
	 *
	 * @group laravel
	 */
	public function testCanCheckTableExists()
	{
		$this->assertTrue(Schema::exists('query_test'));
		$this->assertFalse(Schema::exists('nonexistent_table'));
	}
}
