<?php use Laravel\Lang;

class LangTest extends PHPUnit_Framework_TestCase {

	public function test_simple_language_lines_can_be_retrieved()
	{
		$language = require LANG_PATH.'en/validation.php';

		$this->assertEquals($language['required'], Lang::line('validation.required')->get());
	}

	public function test_default_value_is_returned_when_line_doesnt_exist()
	{
		$language = require LANG_PATH.'en/validation.php';

		$this->assertEquals('Taylor', Lang::line('validation.something')->get(null, 'Taylor'));
	}

	public function test_replacements_can_be_made_on_language_lines()
	{
		$language = require LANG_PATH.'en/validation.php';

		$expect = str_replace(':attribute', 'E-Mail', $language['required']);

		$this->assertEquals($expect, Lang::line('validation.required', array('attribute' => 'E-Mail'))->get());
	}

}