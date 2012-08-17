<?php

class HashTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test the Hash::make method.
	 *
	 * @group laravel
	 */
	public function testHashProducesValidBcryptHash()
	{
		$this->assertTrue(strlen(Hash::make('taylor')) == 60);
	}

	/**
	 * Test the Hash::check method.
	 *
	 * @group laravel
	 */
	public function testHashCheckFailsWhenNotMatching()
	{
		$hash = Hash::make('taylor');

		$this->assertFalse(Hash::check('foo', $hash));
	}

	/**
	 * Test the Hash::check method.
	 *
	 * @group laravel
	 */
	public function testHashCheckPassesWhenMatches()
	{
		$this->assertTrue(Hash::check('taylor', Hash::make('taylor')));
	}

}