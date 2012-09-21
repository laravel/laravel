<?php

class StrTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test the Str::encoding method.
	 *
	 * @group laravel
	 */
	public function testEncodingShouldReturnApplicationEncoding()
	{
		$this->assertEquals('UTF-8', Config::get('application.encoding'));
		Config::set('application.encoding', 'foo');
		$this->assertEquals('foo', Config::get('application.encoding'));
		Config::set('application.encoding', 'UTF-8');
	}

	/**
	 * Test the Str::length method.
	 *
	 * @group laravel
	 */
	public function testStringLengthIsCorrect()
	{
		$this->assertEquals(6, Str::length('Taylor'));
		$this->assertEquals(5, Str::length('ラドクリフ'));
	}

	/**
	 * Test the Str::lower method.
	 *
	 * @group laravel
	 */
	public function testStringCanBeConvertedToLowercase()
	{
		$this->assertEquals('taylor', Str::lower('TAYLOR'));
		$this->assertEquals('άχιστη', Str::lower('ΆΧΙΣΤΗ'));
	}

	/**
	 * Test the Str::upper method.
	 *
	 * @group laravel
	 */
	public function testStringCanBeConvertedToUppercase()
	{
		$this->assertEquals('TAYLOR', Str::upper('taylor'));
		$this->assertEquals('ΆΧΙΣΤΗ', Str::upper('άχιστη'));
	}

	/**
	 * Test the Str::title method.
	 *
	 * @group laravel
	 */
	public function testStringCanBeConvertedToTitleCase()
	{
		$this->assertEquals('Taylor', Str::title('taylor'));
		$this->assertEquals('Άχιστη', Str::title('άχιστη'));
	}

	/**
	 * Test the Str::limit method.
	 *
	 * @group laravel
	 */
	public function testStringCanBeLimitedByCharacters()
	{
		$this->assertEquals('Tay...', Str::limit('Taylor', 3));
		$this->assertEquals('Taylor', Str::limit('Taylor', 6));
		$this->assertEquals('Tay___', Str::limit('Taylor', 3, '___'));
	}

	/**
	 * Test the Str::words method.
	 *
	 * @group laravel
	 */
	public function testStringCanBeLimitedByWords()
	{
		$this->assertEquals('Taylor...', Str::words('Taylor Otwell', 1));
		$this->assertEquals('Taylor___', Str::words('Taylor Otwell', 1, '___'));
		$this->assertEquals('Taylor Otwell', Str::words('Taylor Otwell', 3));
	}

	/**
	 * Test the Str::plural and Str::singular methods.
	 *
	 * @group laravel
	 */
	public function testStringsCanBeSingularOrPlural()
	{
		$this->assertEquals('user', Str::singular('users'));
		$this->assertEquals('users', Str::plural('user'));
		$this->assertEquals('User', Str::singular('Users'));
		$this->assertEquals('Users', Str::plural('User'));
		$this->assertEquals('user', Str::plural('user', 1));
		$this->assertEquals('users', Str::plural('user', 2));
		$this->assertEquals('chassis', Str::plural('chassis', 2));
		$this->assertEquals('traffic', Str::plural('traffic', 2));
	}

	/**
	 * Test the Str::slug method.
	 *
	 * @group laravel
	 */
	public function testStringsCanBeSlugged()
	{
		$this->assertEquals('my-new-post', Str::slug('My nEw post!!!'));
		$this->assertEquals('my_new_post', Str::slug('My nEw post!!!', '_'));
	}

	/**
	 * Test the Str::classify method.
	 *
	 * @group laravel
	 */
	public function testStringsCanBeClassified()
	{
		$this->assertEquals('Something_Else', Str::classify('something.else'));
		$this->assertEquals('Something_Else', Str::classify('something_else'));
	}

	/**
	 * Test the Str::random method.
	 *
	 * @group laravel
	 */
	public function testRandomStringsCanBeGenerated()
	{
		$this->assertEquals(40, strlen(Str::random(40)));
	}

}
