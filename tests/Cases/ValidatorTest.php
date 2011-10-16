<?php

use Laravel\Validation\Validator;

class ValidatorTest extends PHPUnit_Framework_TestCase {

	public function test_simple_group_of_validations()
	{
		$rules = array(
			'email'    => 'required|email',
			'password' => 'required|confirmed|min:6',
			'name'     => 'required|alpha',
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
		$this->assertFalse($validator->errors->has('password'));
	}

}