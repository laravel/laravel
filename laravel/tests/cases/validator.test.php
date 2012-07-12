<?php

class ValidatorTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Config::set('database.default', 'sqlite');
	}

	/**
	 * Tear down the test environment.
	 */
	public function tearDown()
	{
		Config::set('database.default', 'mysql');
		$_FILES = array();
	}

	/**
	 * Test the required validation rule.
	 *
	 * @group laravel
	 */
	public function testRequiredRule()
	{
		$input = array('name' => 'Taylor Otwell');
		$rules = array('name' => 'required');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$input['name'] = '';
		$this->assertFalse(Validator::make($input, $rules)->valid());

		unset($input['name']);
		$this->assertFalse(Validator::make($input, $rules)->valid());

		$_FILES['name']['tmp_name'] = 'foo';
		$this->assertTrue(Validator::make($_FILES, $rules)->valid());

		$_FILES['name']['tmp_name'] = '';
		$this->assertFalse(Validator::make($_FILES, $rules)->valid());
	}

	/**
	 * Test the confirmed validation rule.
	 *
	 * @group laravel
	 */
	public function testTheConfirmedRule()
	{
		$input = array('password' => 'foo', 'password_confirmation' => 'foo');
		$rules = array('password' => 'confirmed');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$input['password_confirmation'] = 'foo_bar';
		$this->assertFalse(Validator::make($input, $rules)->valid());

		unset($input['password_confirmation']);
		$this->assertFalse(Validator::make($input, $rules)->valid());
	}

	/**
	 * Test the different validation rule.
	 *
	 * @group laravel
	 */
	public function testTheDifferentRule()
	{
		$input = array('password' => 'foo', 'password_confirmation' => 'bar');
		$rules = array('password' => 'different:password_confirmation');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$input['password_confirmation'] = 'foo';
		$this->assertFalse(Validator::make($input, $rules)->valid());

		unset($input['password_confirmation']);
		$this->assertFalse(Validator::make($input, $rules)->valid());
	}

	/**
	 * Test the accepted validation rule.
	 *
	 * @group laravel
	 */
	public function testTheAcceptedRule()
	{
		$input = array('terms' => '1');
		$rules = array('terms' => 'accepted');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$input['terms'] = 'yes';
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$input['terms'] = '2';
		$this->assertFalse(Validator::make($input, $rules)->valid());

		// The accepted rule implies required, so should fail if field not present.
		unset($input['terms']);
		$this->assertFalse(Validator::make($input, $rules)->valid());
	}

	/**
	 * Test the numeric validation rule.
	 *
	 * @group laravel
	 */
	public function testTheNumericRule()
	{
		$input = array('amount' => '1.21');
		$rules = array('amount' => 'numeric');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$input['amount'] = '1';
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$input['amount'] = 1.2;
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$input['amount'] = '1.2a';
		$this->assertFalse(Validator::make($input, $rules)->valid());
	}

	/**
	 * Test the integer validation rule.
	 *
	 * @group laravel
	 */
	public function testTheIntegerRule()
	{
		$input = array('amount' => '1');
		$rules = array('amount' => 'integer');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$input['amount'] = '0';
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$input['amount'] = 1.2;
		$this->assertFalse(Validator::make($input, $rules)->valid());

		$input['amount'] = '1.2a';
		$this->assertFalse(Validator::make($input, $rules)->valid());
	}

	/**
	 * Test the size validation rule.
	 *
	 * @group laravel
	 */
	public function testTheSizeRule()
	{
		$input = array('amount' => '1.21');
		$rules = array('amount' => 'numeric|size:1.21');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$rules = array('amount' => 'numeric|size:1');
		$this->assertFalse(Validator::make($input, $rules)->valid());

		// If no numeric rule is on the field, it is treated as a string
		$input = array('amount' => '111');
		$rules = array('amount' => 'size:3');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$rules = array('amount' => 'size:4');
		$this->assertFalse(Validator::make($input, $rules)->valid());

		// The size rules checks kilobytes on files
		$_FILES['photo']['tmp_name'] = 'foo';
		$_FILES['photo']['size'] = 10240;
		$rules = array('photo' => 'size:10');
		$this->assertTrue(Validator::make($_FILES, $rules)->valid());

		$_FILES['photo']['size'] = 14000;
		$this->assertFalse(Validator::make($_FILES, $rules)->valid());
	}

	/**
	 * Test the between validation rule.
	 *
	 * @group laravel
	 */
	public function testTheBetweenRule()
	{
		$input = array('amount' => '1.21');
		$rules = array('amount' => 'numeric|between:1,2');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$rules = array('amount' => 'numeric|between:2,3');
		$this->assertFalse(Validator::make($input, $rules)->valid());

		// If no numeric rule is on the field, it is treated as a string
		$input = array('amount' => '111');
		$rules = array('amount' => 'between:1,3');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$rules = array('amount' => 'between:100,111');
		$this->assertFalse(Validator::make($input, $rules)->valid());

		// The size rules checks kilobytes on files
		$_FILES['photo']['tmp_name'] = 'foo';
		$_FILES['photo']['size'] = 10240;
		$rules = array('photo' => 'between:9,11');
		$this->assertTrue(Validator::make($_FILES, $rules)->valid());

		$_FILES['photo']['size'] = 14000;
		$this->assertFalse(Validator::make($_FILES, $rules)->valid());
	}

	/**
	 * Test the between validation rule.
	 *
	 * @group laravel
	 */
	public function testTheMinRule()
	{
		$input = array('amount' => '1.21');
		$rules = array('amount' => 'numeric|min:1');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$rules = array('amount' => 'numeric|min:2');
		$this->assertFalse(Validator::make($input, $rules)->valid());

		// If no numeric rule is on the field, it is treated as a string
		$input = array('amount' => '01');
		$rules = array('amount' => 'min:2');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$rules = array('amount' => 'min:3');
		$this->assertFalse(Validator::make($input, $rules)->valid());

		// The size rules checks kilobytes on files
		$_FILES['photo']['tmp_name'] = 'foo';
		$_FILES['photo']['size'] = 10240;
		$rules = array('photo' => 'min:9');
		$this->assertTrue(Validator::make($_FILES, $rules)->valid());

		$_FILES['photo']['size'] = 8000;
		$this->assertFalse(Validator::make($_FILES, $rules)->valid());
	}

	/**
	 * Test the between validation rule.
	 *
	 * @group laravel
	 */
	public function testTheMaxRule()
	{
		$input = array('amount' => '1.21');
		$rules = array('amount' => 'numeric|max:2');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$rules = array('amount' => 'numeric|max:1');
		$this->assertFalse(Validator::make($input, $rules)->valid());

		// If no numeric rule is on the field, it is treated as a string
		$input = array('amount' => '01');
		$rules = array('amount' => 'max:3');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$rules = array('amount' => 'max:1');
		$this->assertFalse(Validator::make($input, $rules)->valid());

		// The size rules checks kilobytes on files
		$_FILES['photo']['tmp_name'] = 'foo';
		$_FILES['photo']['size'] = 10240;
		$rules = array('photo' => 'max:11');
		$this->assertTrue(Validator::make($_FILES, $rules)->valid());

		$_FILES['photo']['size'] = 140000;
		$this->assertFalse(Validator::make($_FILES, $rules)->valid());
	}

	/**
	 * Test the in validation rule.
	 *
	 * @group laravel
	 */
	public function testTheInRule()
	{
		$input = array('size' => 'L');
		$rules = array('size' => 'in:S,M,L');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$input['size'] = 'XL';
		$this->assertFalse(Validator::make($input, $rules)->valid());
	}

	/**
	 * Test the not-in validation rule.
	 *
	 * @group laravel
	 */
	public function testTheNotInRule()
	{
		$input = array('size' => 'L');
		$rules = array('size' => 'not_in:S,M,L');
		$this->assertFalse(Validator::make($input, $rules)->valid());

		$input['size'] = 'XL';
		$this->assertTrue(Validator::make($input, $rules)->valid());
	}

	/**
	 * Test the IP validation rule.
	 *
	 * @group laravel
	 */
	public function testTheIPRule()
	{
		$input = array('ip' => '192.168.1.1');
		$rules = array('ip' => 'ip');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$input['ip'] = '192.111';
		$this->assertFalse(Validator::make($input, $rules)->valid());
	}

	/**
	 * Test the e-mail validation rule.
	 *
	 * @group laravel
	 */
	public function testTheEmailRule()
	{
		$input = array('email' => 'example@gmail.com');
		$rules = array('email' => 'email');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$input['email'] = 'blas-asok';
		$this->assertFalse(Validator::make($input, $rules)->valid());
	}

	/**
	 * Test the URL validation rule.
	 *
	 * @group laravel
	 */
	public function testTheUrlRule()
	{
		$input = array('url' => 'http://www.google.com');
		$rules = array('url' => 'url');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$input['url'] = 'blas-asok';
		$this->assertFalse(Validator::make($input, $rules)->valid());
	}

	/**
	 * Test the active URL validation rule.
	 *
	 * @group laravel
	 */
	public function testTheActiveUrlRule()
	{
		$input = array('url' => 'http://google.com');
		$rules = array('url' => 'active_url');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$input['url'] = 'http://asdlk-aselkaiwels.com';
		$this->assertFalse(Validator::make($input, $rules)->valid());
	}

	/**
	 * Test the image validation rule.
	 *
	 * @group laravel
	 */
	public function testTheImageRule()
	{
		$_FILES['photo']['tmp_name'] = path('storage').'files/desert.jpg';
		$rules = array('photo' => 'image');
		$this->assertTrue(Validator::make($_FILES, $rules)->valid());

		$_FILES['photo']['tmp_name'] = path('app').'routes.php';
		$this->assertFalse(Validator::make($_FILES, $rules)->valid());
	}

	/**
	 * Test the alpha validation rule.
	 *
	 * @group laravel
	 */
	public function testTheAlphaRule()
	{
		$input = array('name' => 'TaylorOtwell');
		$rules = array('name' => 'alpha');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$input['name'] = 'Taylor Otwell';
		$this->assertFalse(Validator::make($input, $rules)->valid());
	}

	/**
	 * Test the alpha_num validation rule.
	 *
	 * @group laravel
	 */
	public function testTheAlphaNumRule()
	{
		$input = array('name' => 'TaylorOtwell1');
		$rules = array('name' => 'alpha_num');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$input['name'] = 'Taylor Otwell';
		$this->assertFalse(Validator::make($input, $rules)->valid());
	}

	/**
	 * Test the alpha_num validation rule.
	 *
	 * @group laravel
	 */
	public function testTheAlphaDashRule()
	{
		$input = array('name' => 'Taylor-Otwell_1');
		$rules = array('name' => 'alpha_dash');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$input['name'] = 'Taylor Otwell';
		$this->assertFalse(Validator::make($input, $rules)->valid());
	}

	/**
	 * Test the mimes validation rule.
	 *
	 * @group laravel
	 */
	public function testTheMimesRule()
	{
		$_FILES['file']['tmp_name'] = path('app').'routes.php';
		$rules = array('file' => 'mimes:php,txt');
		$this->assertTrue(Validator::make($_FILES, $rules)->valid());

		$rules = array('file' => 'mimes:jpg,bmp');
		$this->assertFalse(Validator::make($_FILES, $rules)->valid());

		$_FILES['file']['tmp_name'] = path('storage').'files/desert.jpg';
		$rules['file'] = 'mimes:jpg,bmp';
		$this->assertTrue(Validator::make($_FILES, $rules)->valid());

		$rules['file'] = 'mimes:txt,bmp';
		$this->assertFalse(Validator::make($_FILES, $rules)->valid());
	}

	/**
	 * Test the unique validation rule.
	 *
	 * @group laravel
	 */
	public function testUniqueRule()
	{
		$input = array('code' => 'ZZ');
		$rules = array('code' => 'unique:validation_unique');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$input = array('code' => 'AR');
		$this->assertFalse(Validator::make($input, $rules)->valid());

		$rules = array('code' => 'unique:validation_unique,code,AR,code');
		$this->assertTrue(Validator::make($input, $rules)->valid());
	}

	/**
	 * Tests the exists validation rule.
	 *
	 * @group laravel
	 */
	public function testExistsRule()
	{
		$input = array('code' => 'TX');
		$rules = array('code' => 'exists:validation_unique');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$input['code'] = array('TX', 'NY');
		$rules = array('code' => 'exists:validation_unique,code');
		$this->assertTrue(Validator::make($input, $rules)->valid());

		$input['code'] = array('TX', 'XX');
		$this->assertFalse(Validator::make($input, $rules)->valid());

		$input['code'] = 'XX';
		$this->assertFalse(Validator::make($input, $rules)->valid());
	}

	/**
	 * Test that the validator sets the correct messages.
	 *
	 * @group laravel
	 */
	public function testCorrectMessagesAreSet()
	{
		$lang = require path('app').'language/en/validation.php';
	
		$input = array('email' => 'example-foo');
		$rules = array('name' => 'required', 'email' => 'required|email');
		$v = Validator::make($input, $rules);
		$v->valid();
		$messages = $v->errors;
		$this->assertInstanceOf('Laravel\\Messages', $messages);
		$this->assertEquals(str_replace(':attribute', 'name', $lang['required']), $messages->first('name'));
		$this->assertEquals(str_replace(':attribute', 'email', $lang['email']), $messages->first('email'));
	}

	/**
	 * Test that custom messages are recognized.
	 *
	 * @group laravel
	 */
	public function testCustomMessagesAreRecognize()
	{
		$messages = array('required' => 'Required!');
		$rules = array('name' => 'required');
		$v = Validator::make(array(), $rules, $messages);
		$v->valid();
		$this->assertEquals('Required!', $v->errors->first('name'));

		$messages['email_required'] = 'Email Required!';
		$rules = array('name' => 'required', 'email' => 'required');
		$v = Validator::make(array(), $rules, $messages);
		$v->valid();
		$this->assertEquals('Required!', $v->errors->first('name'));
		$this->assertEquals('Email Required!', $v->errors->first('email'));

		$rules = array('custom' => 'required');
		$v = Validator::make(array(), $rules);
		$v->valid();
		$this->assertEquals('This field is required!', $v->errors->first('custom'));
	}

	/**
	 * Test that size replacements are made on messages.
	 *
	 * @group laravel
	 */
	public function testNumericSizeReplacementsAreMade()
	{
		$lang = require path('app').'language/en/validation.php';
		
		$input = array('amount' => 100);
		$rules = array('amount' => 'numeric|size:80');
		$v = Validator::make($input, $rules);
		$v->valid();
		$this->assertEquals(str_replace(array(':attribute', ':size'), array('amount', '80'), $lang['size']['numeric']), $v->errors->first('amount'));

		$rules = array('amount' => 'numeric|between:70,80');
		$v = Validator::make($input, $rules);
		$v->valid();
		$expect = str_replace(array(':attribute', ':min', ':max'), array('amount', '70', '80'), $lang['between']['numeric']);
		$this->assertEquals($expect, $v->errors->first('amount'));

		$rules = array('amount' => 'numeric|min:120');
		$v = Validator::make($input, $rules);
		$v->valid();
		$expect = str_replace(array(':attribute', ':min'), array('amount', '120'), $lang['min']['numeric']);
		$this->assertEquals($expect, $v->errors->first('amount'));

		$rules = array('amount' => 'numeric|max:20');
		$v = Validator::make($input, $rules);
		$v->valid();
		$expect = str_replace(array(':attribute', ':max'), array('amount', '20'), $lang['max']['numeric']);
		$this->assertEquals($expect, $v->errors->first('amount'));
	}

	/**
	 * Test that string size replacements are made on messages.
	 *
	 * @group laravel
	 */
	public function testStringSizeReplacementsAreMade()
	{
		$lang = require path('app').'language/en/validation.php';
		
		$input = array('amount' => '100');
		$rules = array('amount' => 'size:80');
		$v = Validator::make($input, $rules);
		$v->valid();
		$this->assertEquals(str_replace(array(':attribute', ':size'), array('amount', '80'), $lang['size']['string']), $v->errors->first('amount'));

		$rules = array('amount' => 'between:70,80');
		$v = Validator::make($input, $rules);
		$v->valid();
		$expect = str_replace(array(':attribute', ':min', ':max'), array('amount', '70', '80'), $lang['between']['string']);
		$this->assertEquals($expect, $v->errors->first('amount'));

		$rules = array('amount' => 'min:120');
		$v = Validator::make($input, $rules);
		$v->valid();
		$expect = str_replace(array(':attribute', ':min'), array('amount', '120'), $lang['min']['string']);
		$this->assertEquals($expect, $v->errors->first('amount'));

		$rules = array('amount' => 'max:2');
		$v = Validator::make($input, $rules);
		$v->valid();
		$expect = str_replace(array(':attribute', ':max'), array('amount', '2'), $lang['max']['string']);
		$this->assertEquals($expect, $v->errors->first('amount'));
	}

	/**
	 * Test that string size replacements are made on messages.
	 *
	 * @group laravel
	 */
	public function testFileSizeReplacementsAreMade()
	{
		$lang = require path('app').'language/en/validation.php';
		
		$_FILES['amount']['tmp_name'] = 'foo';
		$_FILES['amount']['size'] = 10000;
		$rules = array('amount' => 'size:80');
		$v = Validator::make($_FILES, $rules);
		$v->valid();
		$this->assertEquals(str_replace(array(':attribute', ':size'), array('amount', '80'), $lang['size']['file']), $v->errors->first('amount'));

		$rules = array('amount' => 'between:70,80');
		$v = Validator::make($_FILES, $rules);
		$v->valid();
		$expect = str_replace(array(':attribute', ':min', ':max'), array('amount', '70', '80'), $lang['between']['file']);
		$this->assertEquals($expect, $v->errors->first('amount'));

		$rules = array('amount' => 'min:120');
		$v = Validator::make($_FILES, $rules);
		$v->valid();
		$expect = str_replace(array(':attribute', ':min'), array('amount', '120'), $lang['min']['file']);
		$this->assertEquals($expect, $v->errors->first('amount'));

		$rules = array('amount' => 'max:2');
		$v = Validator::make($_FILES, $rules);
		$v->valid();
		$expect = str_replace(array(':attribute', ':max'), array('amount', '2'), $lang['max']['file']);
		$this->assertEquals($expect, $v->errors->first('amount'));
	}

	/**
	 * Test that values get replaced in messages.
	 *
	 * @group laravel
	 */
	public function testValuesGetReplaced()
	{
		$lang = require path('app').'language/en/validation.php';

		$_FILES['file']['tmp_name'] = path('storage').'files/desert.jpg';
		$rules = array('file' => 'mimes:php,txt');
		$v = Validator::make($_FILES, $rules);
		$v->valid();

		$expect = str_replace(array(':attribute', ':values'), array('file', 'php, txt'), $lang['mimes']);
		$this->assertEquals($expect, $v->errors->first('file'));
	}

	/**
	 * Test custom attribute names are replaced.
	 *
	 * @group laravel
	 */
	public function testCustomAttributesAreReplaced()
	{
		$lang = require path('app').'language/en/validation.php';

		$rules = array('test_attribute' => 'required');
		$v = Validator::make(array(), $rules);
		$v->valid();

		$expect = str_replace(':attribute', 'attribute', $lang['required']);
		$this->assertEquals($expect, $v->errors->first('test_attribute'));
	}

}