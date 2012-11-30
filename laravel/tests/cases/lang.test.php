<?php

class LangTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test the Lang::line method.
	 *
	 * @group laravel
	 */
	public function testGetMethodCanGetFromDefaultLanguage()
	{
		$validation = require path('app').'language/en/validation.php';

		$this->assertEquals($validation['required'], Lang::line('validation.required')->get());
		$this->assertEquals('Taylor', Lang::line('validation.foo')->get(null, 'Taylor'));
	}

	/**
	 * Test the Lang::line method.
	 *
	 * @group laravel
	 */
	public function testGetMethodCanGetLinesForAGivenLanguage()
	{
		$validation = require path('app').'language/sp/validation.php';

		$this->assertEquals($validation['required'], Lang::line('validation.required')->get('sp'));
	}

	/**
	 * Test the __toString method.
	 *
	 * @group laravel
	 */
	public function testLineCanBeCastAsString()
	{
		$validation = require path('app').'language/en/validation.php';

		$this->assertEquals($validation['required'], (string) Lang::line('validation.required'));
	}

	/**
	 * Test that string replacements are made on lines.
	 *
	 * @group laravel
	 */
	public function testReplacementsAreMadeOnLines()
	{
		$validation = require path('app').'language/en/validation.php';

		$line = str_replace(':attribute', 'e-mail', $validation['required']);

		$this->assertEquals($line, Lang::line('validation.required', array('attribute' => 'e-mail'))->get());
	}

	/**
	 * Test the Lang::has method.
	 *
	 * @group laravel
	 */
	public function testHasMethodIndicatesIfLangaugeLineExists()
	{
		$this->assertTrue(Lang::has('validation'));
		$this->assertTrue(Lang::has('validation.required'));
		$this->assertFalse(Lang::has('validation.foo'));
	}

}