<?php

class QueryTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test the "find" method.
	 *
	 * @group laravel
	 */
	public function testFindMethodCanReturnByID()
	{
		$this->assertEquals('taylor@example.com', $this->query()->find(1)->email);
	}

	/**
	 * Test the select method.
	 *
	 * @group laravel
	 */
	public function testSelectMethodLimitsColumns()
	{
		$result = $this->query()->select(array('email'))->first();

		$this->assertTrue(isset($result->email));
		$this->assertFalse(isset($result->name));
	}

	/**
	 * Test the raw_where method.
	 *
	 * @group laravel
	 */
	public function testRawWhereCanBeUsed()
	{
		
	}

	/**
	 * Get the query instance for the test case.
	 *
	 * @return Query
	 */
	protected function query()
	{
		return DB::table('query_test');
	}

}