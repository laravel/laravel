<?php

use Laravel\Lang;
use Laravel\Validation\Validator;

class ValidatorTest extends PHPUnit_Framework_TestCase {

	public function test_simple_group_of_validations()
	{
		$rules = array(
			'email'    => 'required|email',
			'password' => 'required|confirmed|min:6',
			'name'     => 'required|alpha',
			'age'      => 'required',
		);

		$attributes = array(
			'email'                 => 'taylorotwell',
			'password'              => 'something',
			'password_confirmation' => 'something',
			'name'                  => 'taylor5',
		);

		$messages = array('name_alpha' => 'The name must be alphabetic!');

		$validator = Validator::make($attributes, $rules, $messages);

		$this->assertFalse($validator->valid());
		$this->assertTrue($validator->errors->has('name'));
		$this->assertTrue($validator->errors->has('email'));
		$this->assertFalse($validator->errors->has('password'));
		$this->assertEquals(count($validator->errors->get('name')), 1);
		$this->assertEquals($validator->errors->first('name'), 'The name must be alphabetic!');
		$this->assertEquals($validator->errors->first('email'), Lang::line('validation.email', array('attribute' => 'email'))->get());
		$this->assertEquals($validator->errors->first('age'), Lang::line('validation.required', array('attribute' => 'age'))->get());
	}

}