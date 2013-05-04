<?php

class FormTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		URL::$base = null;
		Config::set('application.url', 'http://localhost');
		Config::set('application.index', 'index.php');
	}
	/**
	 * Destroy the test enviornment.
	 */
	public function tearDown()
	{
		Config::set('application.url', '');
		Config::set('application.index', 'index.php');
	}

	/**
	 * Test the compilation of opening a form
	 * 
	 * @group laravel
	 */
	public function testOpeningForm()
	{
		$form1 = Form::open('foobar', 'GET');
		$form2 = Form::open('foobar', 'POST');
		$form3 = Form::open('foobar', 'PUT', array('accept-charset' => 'UTF-16', 'class' => 'form'));
		$form4 = Form::open('foobar', 'DELETE', array('class' => 'form'));

		$this->assertEquals('<form method="GET" action="http://localhost/index.php/foobar" accept-charset="UTF-8">', $form1);
		$this->assertEquals('<form method="POST" action="http://localhost/index.php/foobar" accept-charset="UTF-8">', $form2);
		$this->assertEquals('<form accept-charset="UTF-16" class="form" method="POST" action="http://localhost/index.php/foobar"><input type="hidden" name="_method" value="PUT">', $form3);
		$this->assertEquals('<form class="form" method="POST" action="http://localhost/index.php/foobar" accept-charset="UTF-8"><input type="hidden" name="_method" value="DELETE">', $form4);
	}

	/**
	 * Test the compilation of opening a secure form
	 * 
	 * @group laravel
	 */
	public function testOpeningFormSecure()
	{
		$form1 = Form::open_secure('foobar', 'GET');
		$form2 = Form::open_secure('foobar', 'POST');
		$form3 = Form::open_secure('foobar', 'PUT', array('accept-charset' => 'UTF-16', 'class' => 'form'));
		$form4 = Form::open_secure('foobar', 'DELETE', array('class' => 'form'));

		$this->assertEquals('<form method="GET" action="https://localhost/index.php/foobar" accept-charset="UTF-8">', $form1);
		$this->assertEquals('<form method="POST" action="https://localhost/index.php/foobar" accept-charset="UTF-8">', $form2);
		$this->assertEquals('<form accept-charset="UTF-16" class="form" method="POST" action="https://localhost/index.php/foobar"><input type="hidden" name="_method" value="PUT">', $form3);
		$this->assertEquals('<form class="form" method="POST" action="https://localhost/index.php/foobar" accept-charset="UTF-8"><input type="hidden" name="_method" value="DELETE">', $form4);
	}

	/**
	 * Test the compilation of opening a form for files
	 * 
	 * @group laravel
	 */
	public function testOpeningFormForFile()
	{
		$form1 = Form::open_for_files('foobar', 'GET');
		$form2 = Form::open_for_files('foobar', 'POST');
		$form3 = Form::open_for_files('foobar', 'PUT', array('accept-charset' => 'UTF-16', 'class' => 'form'));
		$form4 = Form::open_for_files('foobar', 'DELETE', array('class' => 'form'));

		$this->assertEquals('<form enctype="multipart/form-data" method="GET" action="http://localhost/index.php/foobar" accept-charset="UTF-8">', $form1);
		$this->assertEquals('<form enctype="multipart/form-data" method="POST" action="http://localhost/index.php/foobar" accept-charset="UTF-8">', $form2);
		$this->assertEquals('<form accept-charset="UTF-16" class="form" enctype="multipart/form-data" method="POST" action="http://localhost/index.php/foobar"><input type="hidden" name="_method" value="PUT">', $form3);
		$this->assertEquals('<form class="form" enctype="multipart/form-data" method="POST" action="http://localhost/index.php/foobar" accept-charset="UTF-8"><input type="hidden" name="_method" value="DELETE">', $form4);
	}

	/**
	 * Test the compilation of opening a secure form for files
	 * 
	 * @group laravel
	 */
	public function testOpeningFormSecureForFile()
	{
		$form1 = Form::open_secure_for_files('foobar', 'GET');
		$form2 = Form::open_secure_for_files('foobar', 'POST');
		$form3 = Form::open_secure_for_files('foobar', 'PUT', array('accept-charset' => 'UTF-16', 'class' => 'form'));
		$form4 = Form::open_secure_for_files('foobar', 'DELETE', array('class' => 'form'));

		$this->assertEquals('<form enctype="multipart/form-data" method="GET" action="https://localhost/index.php/foobar" accept-charset="UTF-8">', $form1);
		$this->assertEquals('<form enctype="multipart/form-data" method="POST" action="https://localhost/index.php/foobar" accept-charset="UTF-8">', $form2);
		$this->assertEquals('<form accept-charset="UTF-16" class="form" enctype="multipart/form-data" method="POST" action="https://localhost/index.php/foobar"><input type="hidden" name="_method" value="PUT">', $form3);
		$this->assertEquals('<form class="form" enctype="multipart/form-data" method="POST" action="https://localhost/index.php/foobar" accept-charset="UTF-8"><input type="hidden" name="_method" value="DELETE">', $form4);
	}

	/**
	 * Test the compilation of closing a form
	 * 
	 * @group laravel
	 */
	public function testClosingForm()
	{
		$this->assertEquals('</form>', Form::close());
	}

	/**
	 * Test the compilation of form label
	 * 
	 * @group laravel
	 */
	public function testFormLabel()
	{
		$form1 = Form::label('foo', 'Foobar');
		$form2 = Form::label('foo', 'Foobar', array('class' => 'control-label'));
		$form3 = Form::label('foo', 'Foobar <i>baz</i>', null, false);

		$this->assertEquals('<label for="foo">Foobar</label>', $form1);
		$this->assertEquals('<label for="foo" class="control-label">Foobar</label>', $form2);
		$this->assertEquals('<label for="foo">Foobar <i>baz</i></label>', $form3);
	}

	/**
	 * Test the compilation of form input
	 * 
	 * @group laravel
	 */
	public function testFormInput()
	{
		$form1 = Form::input('text', 'foo');
		$form2 = Form::input('text', 'foo', 'foobar');
		$form3 = Form::input('date', 'foobar', null, array('class' => 'span2'));

		$this->assertEquals('<input type="text" name="foo" id="foo">', $form1);
		$this->assertEquals('<input type="text" name="foo" value="foobar" id="foo">', $form2);
		$this->assertEquals('<input class="span2" type="date" name="foobar">', $form3);
	}

	/**
	 * Test the compilation of form text
	 * 
	 * @group laravel
	 */
	public function testFormText()
	{
		$form1 = Form::input('text', 'foo');
		$form2 = Form::text('foo');
		$form3 = Form::text('foo', 'foobar');
		$form4 = Form::text('foo', null, array('class' => 'span2'));

		$this->assertEquals('<input type="text" name="foo" id="foo">', $form1);
		$this->assertEquals($form1, $form2);
		$this->assertEquals('<input type="text" name="foo" value="foobar" id="foo">', $form3);
		$this->assertEquals('<input class="span2" type="text" name="foo" id="foo">', $form4);
	}

	/**
	 * Test the compilation of form password
	 * 
	 * @group laravel
	 */
	public function testFormPassword()
	{
		$form1 = Form::input('password', 'foo');
		$form2 = Form::password('foo');
		$form3 = Form::password('foo', array('class' => 'span2'));

		$this->assertEquals('<input type="password" name="foo" id="foo">', $form1);
		$this->assertEquals($form1, $form2);
		$this->assertEquals('<input class="span2" type="password" name="foo" id="foo">', $form3);
	}

	/**
	 * Test the compilation of form hidden
	 * 
	 * @group laravel
	 */
	public function testFormHidden()
	{
		$form1 = Form::input('hidden', 'foo');
		$form2 = Form::hidden('foo');
		$form3 = Form::hidden('foo', 'foobar');
		$form4 = Form::hidden('foo', null, array('class' => 'span2'));

		$this->assertEquals('<input type="hidden" name="foo" id="foo">', $form1);
		$this->assertEquals($form1, $form2);
		$this->assertEquals('<input type="hidden" name="foo" value="foobar" id="foo">', $form3);
		$this->assertEquals('<input class="span2" type="hidden" name="foo" id="foo">', $form4);
	}

	/**
	 * Test the compilation of form search
	 * 
	 * @group laravel
	 */
	public function testFormSearch()
	{
		$form1 = Form::input('search', 'foo');
		$form2 = Form::search('foo');
		$form3 = Form::search('foo', 'foobar');
		$form4 = Form::search('foo', null, array('class' => 'span2'));

		$this->assertEquals('<input type="search" name="foo" id="foo">', $form1);
		$this->assertEquals($form1, $form2);
		$this->assertEquals('<input type="search" name="foo" value="foobar" id="foo">', $form3);
		$this->assertEquals('<input class="span2" type="search" name="foo" id="foo">', $form4);
	}

	/**
	 * Test the compilation of form email
	 * 
	 * @group laravel
	 */
	public function testFormEmail()
	{
		$form1 = Form::input('email', 'foo');
		$form2 = Form::email('foo');
		$form3 = Form::email('foo', 'foobar');
		$form4 = Form::email('foo', null, array('class' => 'span2'));

		$this->assertEquals('<input type="email" name="foo" id="foo">', $form1);
		$this->assertEquals($form1, $form2);
		$this->assertEquals('<input type="email" name="foo" value="foobar" id="foo">', $form3);
		$this->assertEquals('<input class="span2" type="email" name="foo" id="foo">', $form4);
	}

	/**
	 * Test the compilation of form telephone
	 * 
	 * @group laravel
	 */
	public function testFormTelephone()
	{
		$form1 = Form::input('tel', 'foo');
		$form2 = Form::telephone('foo');
		$form3 = Form::telephone('foo', 'foobar');
		$form4 = Form::telephone('foo', null, array('class' => 'span2'));

		$this->assertEquals('<input type="tel" name="foo" id="foo">', $form1);
		$this->assertEquals($form1, $form2);
		$this->assertEquals('<input type="tel" name="foo" value="foobar" id="foo">', $form3);
		$this->assertEquals('<input class="span2" type="tel" name="foo" id="foo">', $form4);
	}

	/**
	 * Test the compilation of form url
	 * 
	 * @group laravel
	 */
	public function testFormUrl()
	{
		$form1 = Form::input('url', 'foo');
		$form2 = Form::url('foo');
		$form3 = Form::url('foo', 'foobar');
		$form4 = Form::url('foo', null, array('class' => 'span2'));

		$this->assertEquals('<input type="url" name="foo" id="foo">', $form1);
		$this->assertEquals($form1, $form2);
		$this->assertEquals('<input type="url" name="foo" value="foobar" id="foo">', $form3);
		$this->assertEquals('<input class="span2" type="url" name="foo" id="foo">', $form4);
	}

	/**
	 * Test the compilation of form number
	 * 
	 * @group laravel
	 */
	public function testFormNumber()
	{
		$form1 = Form::input('number', 'foo');
		$form2 = Form::number('foo');
		$form3 = Form::number('foo', 'foobar');
		$form4 = Form::number('foo', null, array('class' => 'span2'));

		$this->assertEquals('<input type="number" name="foo" id="foo">', $form1);
		$this->assertEquals($form1, $form2);
		$this->assertEquals('<input type="number" name="foo" value="foobar" id="foo">', $form3);
		$this->assertEquals('<input class="span2" type="number" name="foo" id="foo">', $form4);
	}

	/**
	 * Test the compilation of form date
	 * 
	 * @group laravel
	 */
	public function testFormDate()
	{
		$form1 = Form::input('date', 'foo');
		$form2 = Form::date('foo');
		$form3 = Form::date('foo', 'foobar');
		$form4 = Form::date('foo', null, array('class' => 'span2'));

		$this->assertEquals('<input type="date" name="foo" id="foo">', $form1);
		$this->assertEquals($form1, $form2);
		$this->assertEquals('<input type="date" name="foo" value="foobar" id="foo">', $form3);
		$this->assertEquals('<input class="span2" type="date" name="foo" id="foo">', $form4);
	}

	/**
	 * Test the compilation of form file
	 * 
	 * @group laravel
	 */
	public function testFormFile()
	{
		$form1 = Form::input('file', 'foo');
		$form2 = Form::file('foo');
		$form3 = Form::file('foo', array('class' => 'span2'));

		$this->assertEquals('<input type="file" name="foo" id="foo">', $form1);
		$this->assertEquals($form1, $form2);
		$this->assertEquals('<input class="span2" type="file" name="foo" id="foo">', $form3);
	}

	/**
	 * Test the compilation of form textarea
	 * 
	 * @group laravel
	 */
	public function testFormTextarea()
	{
		$form1 = Form::textarea('foo');
		$form2 = Form::textarea('foo', 'foobar');
		$form3 = Form::textarea('foo', null, array('class' => 'span2'));

		$this->assertEquals('<textarea name="foo" id="foo" rows="10" cols="50"></textarea>', $form1);
		$this->assertEquals('<textarea name="foo" id="foo" rows="10" cols="50">foobar</textarea>', $form2);
		$this->assertEquals('<textarea class="span2" name="foo" id="foo" rows="10" cols="50"></textarea>', $form3);
	}

	/**
	 * Test the compilation of form select
	 * 
	 * @group laravel
	 */
	public function testFormSelect()
	{
		$select1 = array(
			'foobar' => 'Foobar',
			'hello'  => 'Hello World',
		);

		$select2 = array(
			'foo' => array(
				'foobar' => 'Foobar',
			),
			'hello'  => 'Hello World',
		);

		$form1 = Form::select('foo');
		$form2 = Form::select('foo', $select1, 'foobar');
		$form3 = Form::select('foo', $select1, null, array('class' => 'span2'));
		$form4 = Form::select('foo', $select2, 'foobar');

		$this->assertEquals('<select id="foo" name="foo"></select>', $form1);
		$this->assertEquals('<select id="foo" name="foo"><option value="foobar" selected="selected">Foobar</option><option value="hello">Hello World</option></select>', $form2);
		$this->assertEquals('<select class="span2" id="foo" name="foo"><option value="foobar">Foobar</option><option value="hello">Hello World</option></select>', $form3);
		$this->assertEquals('<select id="foo" name="foo"><optgroup label="foo"><option value="foobar" selected="selected">Foobar</option></optgroup><option value="hello">Hello World</option></select>', $form4);
	}

	/**
	 * Test the compilation of form checkbox
	 * 
	 * @group laravel
	 */
	public function testFormCheckbox()
	{
		$form1 = Form::input('checkbox', 'foo');
		$form2 = Form::checkbox('foo');
		$form3 = Form::checkbox('foo', 'foobar', true);
		$form4 = Form::checkbox('foo', 'foobar', false, array('class' => 'span2'));

		$this->assertEquals('<input type="checkbox" name="foo" id="foo">', $form1);
		$this->assertEquals('<input id="foo" type="checkbox" name="foo" value="1">', $form2);
		$this->assertEquals('<input checked="checked" id="foo" type="checkbox" name="foo" value="foobar">', $form3);
		$this->assertEquals('<input class="span2" id="foo" type="checkbox" name="foo" value="foobar">', $form4);
	}

	/**
	 * Test the compilation of form date
	 * 
	 * @group laravel
	 */
	public function testFormRadio()
	{
		$form1 = Form::input('radio', 'foo');
		$form2 = Form::radio('foo');
		$form3 = Form::radio('foo', 'foobar', true);
		$form4 = Form::radio('foo', 'foobar', false, array('class' => 'span2'));

		$this->assertEquals('<input type="radio" name="foo" id="foo">', $form1);
		$this->assertEquals('<input id="foo" type="radio" name="foo" value="foo">', $form2);
		$this->assertEquals('<input checked="checked" id="foo" type="radio" name="foo" value="foobar">', $form3);
		$this->assertEquals('<input class="span2" id="foo" type="radio" name="foo" value="foobar">', $form4);
	}

	/**
	 * Test the compilation of form submit
	 * 
	 * @group laravel
	 */
	public function testFormSubmit()
	{
		$form1 = Form::submit('foo');
		$form2 = Form::submit('foo', array('class' => 'span2'));

		$this->assertEquals('<input type="submit" value="foo">', $form1);
		$this->assertEquals('<input class="span2" type="submit" value="foo">', $form2);
	}

	/**
	 * Test the compilation of form reset
	 * 
	 * @group laravel
	 */
	public function testFormReset()
	{
		$form1 = Form::reset('foo');
		$form2 = Form::reset('foo', array('class' => 'span2'));

		$this->assertEquals('<input type="reset" value="foo">', $form1);
		$this->assertEquals('<input class="span2" type="reset" value="foo">', $form2);
	}

	/**
	 * Test the compilation of form image
	 * 
	 * @group laravel
	 */
	public function testFormImage()
	{
		$form1 = Form::image('foo/bar', 'foo');
		$form2 = Form::image('foo/bar', 'foo', array('class' => 'span2'));
		$form3 = Form::image('http://google.com/foobar', 'foobar');

		$this->assertEquals('<input src="http://localhost/foo/bar" type="image" name="foo" id="foo">', $form1);
		$this->assertEquals('<input class="span2" src="http://localhost/foo/bar" type="image" name="foo" id="foo">', $form2);
		$this->assertEquals('<input src="http://google.com/foobar" type="image" name="foobar">', $form3);

	}

	/**
	 * Test the compilation of form button
	 * 
	 * @group laravel
	 */
	public function testFormButton()
	{
		$form1 = Form::button('foo');
		$form2 = Form::button('foo', array('class' => 'span2'));

		$this->assertEquals('<button>foo</button>', $form1);
		$this->assertEquals('<button class="span2">foo</button>', $form2);
	}
}