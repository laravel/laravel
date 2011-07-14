<?php

class InputTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass()
	{
		System\Input::$input = null;
	}

	public static function tearDownAfterClass()
	{
		System\Config::set('session.driver', '');
		System\Session::$session = array();
	}

	public function setUp()
	{
		$_SERVER['REQUEST_METHOD'] = 'GET';
	}

	public function tearDown()
	{
		System\Input::$input = null;
	}

	/**
	 * @dataProvider inputByMethodProvider
	 */
	public function testInputShouldHydrateBasedOnRequestMethod($method, $data)
	{
		$_SERVER['REQUEST_METHOD'] = $method;

		$_GET = $data;
		$_POST = $data;

		$this->assertEquals(System\Input::get(), $data);
	}

	public function inputByMethodProvider()
	{
		return array(
			array('GET', array('method' => 'GET')),
			array('POST', array('method' => 'POST')),
		);
	}

	/**
	 * @dataProvider inputBySpoofedMethodProvider
	 */
	public function testInputShouldHydrateBasedOnSpoofedRequestMethod($method, $data)
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_POST = $data;

		$this->assertEquals(System\Input::get(), $data);
	}

	public function inputBySpoofedMethodProvider()
	{
		return array(
			array('PUT', array('request_method' => 'PUT', 'method' => 'PUT')),
			array('DELETE', array('request_method' => 'DELETE', 'method' => 'DELETE')),
		);
	}

	public function testHasMethodReturnsTrueIfItemIsPresentInInputData()
	{
		System\Input::$input = array('name' => 'taylor');
		$this->assertTrue(System\Input::has('name'));
	}

	public function testHasMethodReturnsFalseIfItemIsNotPresentInInputData()
	{
		System\Input::$input = array();
		$this->assertFalse(System\Input::has('name'));
	}

	public function testHasMethodReturnsFalseIfItemIsInInputButIsEmptyString()
	{
		System\Input::$input = array('name' => '');
		$this->assertFalse(System\Input::has('name'));
	}

	public function testGetMethodReturnsItemByInputKey()
	{
		System\Input::$input = array('name' => 'taylor');
		$this->assertEquals(System\Input::get('name'), 'taylor');
	}

	public function testGetMethodReturnsDefaultValueWhenItemDoesntExist()
	{
		System\Input::$input = array();

		$this->assertNull(System\Input::get('name'));
		$this->assertEquals(System\Input::get('name', 'test'), 'test');
		$this->assertEquals(System\Input::get('name', function() {return 'test';}), 'test');
		$this->assertTrue(is_array(System\Input::get()) and count(System\Input::get()) == 0);
	}

	public function testGetMethodReturnsEntireInputArrayWhenNoKeyGiven()
	{
		System\Input::$input = array('name' => 'taylor', 'age' => 25);
		$this->assertEquals(System\Input::get(), System\Input::$input);
	}

	public function testFileMethodReturnsItemFromGlobalFilesArray()
	{
		$_FILES['test'] = array('name' => 'taylor');
		$this->assertEquals(System\Input::file('test'), $_FILES['test']);
	}

	public function testFileMethodReturnsSpecificItemFromFileArrayWhenSpecified()
	{
		$_FILES['test'] = array('size' => 500);
		$this->assertEquals(System\Input::file('test.size'), 500);
	}

	public function testAllMethodReturnsBothGetAndFileArrays()
	{
		$_GET['name'] = 'test';
		$_FILES['picture'] = array();

		$this->assertArrayHasKey('name', System\Input::all());
		$this->assertArrayHasKey('picture', System\Input::all());
	}

	/**
	 * @expectedException Exception
	 */
	public function testOldMethodShouldThrowExceptionWhenSessionsArentEnabled()
	{
		System\Input::old();
	}

	/**
	 * @expectedException Exception
	 */
	public function testHadMethodShouldThrowExceptionWhenSessionsArentEnabled()
	{
		System\Input::has();
	}

	public function testOldMethodShouldReturnOldInputDataFromSession()
	{
		System\Config::set('session.driver', 'test');
		System\Session::$session['data']['laravel_old_input'] = array('name' => 'taylor');

		$this->assertEquals(System\Input::old('name'), 'taylor');
	}

	public function testOldMethodReturnsDefaultValueWhenItemDoesntExist()
	{
		System\Config::set('session.driver', 'test');
		System\Session::$session['data']['laravel_old_input'] = array();

		$this->assertNull(System\Input::old('name'));
		$this->assertEquals(System\Input::old('name', 'test'), 'test');
		$this->assertEquals(System\Input::old('name', function() {return 'test';}), 'test');
		$this->assertTrue(is_array(System\Input::old()) and count(System\Input::old()) == 0);
	}

	public function testHadMethodReturnsTrueIfItemIsPresentInOldInputData()
	{
		System\Config::set('session.driver', 'test');
		System\Session::$session['data']['laravel_old_input'] = array('name' => 'taylor');

		$this->assertTrue(System\Input::had('name'));
	}

	public function testHadMethodReturnsFalseIfItemIsNotPresentInOldInputData()
	{
		System\Config::set('session.driver', 'test');
		System\Session::$session['data']['laravel_old_input'] = array();

		$this->assertFalse(System\Input::had('name'));
	}

}