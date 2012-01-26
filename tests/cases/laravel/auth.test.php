<?php

class AuthTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test the Auth::user method.
	 *
	 * @group laravel
	 */
	public function testUserMethodReturnsCurrentUser()
	{
		Auth::$user = 'Taylor';

		$this->assertEquals('Taylor', Auth::user());
	}

	/**
	 * Test the Auth::check method.
	 *
	 * @group laravel
	 */
	public function testCheckMethodReturnsTrueWhenUserIsSet()
	{
		$this->assertTrue(AuthUserReturnsDummy::check());
	}

	/**
	 * Test the Auth::check method.
	 *
	 * @group laravel
	 */
	public function testCheckMethodReturnsFalseWhenNoUserIsSet()
	{
		$this->assertFalse(AuthUserReturnsNull::check());
	}

	/**
	 * Test the Auth::guest method.
	 *
	 * @group laravel
	 */
	public function testGuestReturnsTrueWhenNoUserIsSet()
	{
		$this->assertTrue(AuthUserReturnsNull::guest());
	}

	/**
	 * Test the Auth::guest method.
	 *
	 * @group laravel
	 */
	public function testGuestReturnsFalseWhenUserIsSet()
	{
		$this->assertFalse(AuthUserReturnsDummy::guest());
	}

}

class AuthUserReturnsNull extends Laravel\Auth {

	public static function user() {}

}

class AuthUserReturnsDummy extends Laravel\Auth {

	public static function user() { return 'Taylor'; }

}