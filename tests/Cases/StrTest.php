<?php

class StrTest extends PHPUnit_Framework_TestCase {

	public function test_lower()
	{
		$this->assertEquals('something', Laravel\Str::lower('SomeThing'));
		$this->assertEquals('τάχιστη', Laravel\Str::lower('ΤΆΧΙΣΤΗ'));
	}

	public function test_upper()
	{
		$this->assertEquals('SPEAK LOUDER', Laravel\Str::upper('speak louder'));
		$this->assertEquals('ΤΆΧΙΣΤΗ', Laravel\Str::upper('Τάχιστη'));
	}

	public function test_title()
	{
		$this->assertEquals('This Is A Test', Laravel\Str::title('this is a test'));
		$this->assertEquals('Τάχιστη Τάχιστη', Laravel\Str::title('τάχιστη τάχιστη'));
	}

	public function test_length()
	{
		$this->assertEquals(4, Laravel\Str::length('four'));
		$this->assertEquals(7, Laravel\Str::length('τάχιστη'));
	}

	public function test_ascii()
	{
		$this->assertEquals('Deuxieme Article', Laravel\Str::ascii('Deuxième Article'));
	}

	public function test_random()
	{
		$this->assertEquals(5, strlen(Laravel\Str::random(5)));
	}

	public function test_limit()
	{
		$this->assertEquals('Thi...', Laravel\Str::limit('This is a string of text', 3, '...'));
		$this->assertEquals('This is&nbsp;', Laravel\Str::limit('This is a string of text', 7, '&nbsp;'));
		$this->assertEquals('τάχ', Laravel\Str::limit('τάχιστη', 3, ''));
	}

	public function test_limit_words()
	{
		$this->assertEquals('This is a...', Laravel\Str::limit_words('This is a string of text', 3, '...'));
		$this->assertEquals('This is a string&nbsp;', Laravel\Str::limit_words('This is a string of text', 4, '&nbsp;'));
	}
}