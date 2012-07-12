<?php

class InputTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the testing environment.
	 */
	public function setUp()
	{
		Config::set('application.key', 'foo');
	}

	/**
	 * Tear down the testing environemnt.
	 */
	public function tearDown()
	{
		// @todo clear httpfoundation request data
		Config::set('application.key', '');
		Session::$instance = null;
	}

	/**
	 * Test the Input::all method.
	 *
	 * @group laravel
	 */
	public function testAllMethodReturnsInputAndFiles()
	{
		Request::foundation()->request->add(array('name' => 'Taylor'));
		
		$_FILES = array('age' => 25);

		$this->assertEquals(Input::all(), array('name' => 'Taylor', 'age' => 25));
	}

	/**
	 * Test the Input::has method.
	 *
	 * @group laravel
	 */
	public function testHasMethodIndicatesTheExistenceOfInput()
	{
		$this->assertFalse(Input::has('foo'));

		Request::foundation()->request->add(array('name' => 'Taylor'));

		$this->assertTrue(Input::has('name'));
	}

	/**
	 * Test the Input::get method.
	 *
	 * @group laravel
	 */
	public function testGetMethodReturnsInputValue()
	{
		Request::foundation()->request->add(array('name' => 'Taylor'));

		$this->assertEquals('Taylor', Input::get('name'));
		$this->assertEquals('Default', Input::get('foo', 'Default'));
	}

	/**
	 * Test the Input::only method.
	 *
	 * @group laravel
	 */
	public function testOnlyMethodReturnsSubsetOfInput()
	{
		Request::foundation()->request->add(array('name' => 'Taylor', 'age' => 25));

		$this->assertEquals(array('name' => 'Taylor'), Input::only(array('name')));
	}

	/**
	 * Test the Input::except method.
	 *
	 * @group laravel
	 */
	public function testExceptMethodReturnsSubsetOfInput()
	{
		Request::foundation()->request->add(array('name' => 'Taylor', 'age' => 25));

		$this->assertEquals(array('age' => 25), Input::except(array('name')));
	}

	/**
	 * Test the Input::old method.
	 *
	 * @group laravel
	 */
	public function testOldInputCanBeRetrievedFromSession()
	{
		$this->setSession();

		Session::$instance->session['data']['laravel_old_input'] = array('name' => 'Taylor');

		$this->assertNull(Input::old('foo'));
		$this->assertTrue(Input::had('name'));
		$this->assertFalse(Input::had('foo'));
		$this->assertEquals('Taylor', Input::old('name'));
	}

	/**
	 * Test the Input::file method.
	 *
	 * @group laravel
	 */
	public function testFileMethodReturnsFromFileArray()
	{
		$_FILES['foo'] = array('name' => 'Taylor', 'size' => 100);

		$this->assertEquals('Taylor', Input::file('foo.name'));
		$this->assertEquals(array('name' => 'Taylor', 'size' => 100), Input::file('foo'));
	}

	/**
	 * Test the Input::flash method.
	 *
	 * @group laravel
	 */
	public function testFlashMethodFlashesInputToSession()
	{
		$this->setSession();

		$input = array('name' => 'Taylor', 'age' => 25);
		Request::foundation()->request->add($input);

		Input::flash();

		$this->assertEquals($input, Session::$instance->session['data'][':new:']['laravel_old_input']);

		Input::flash('only', array('name'));

		$this->assertEquals(array('name' => 'Taylor'), Session::$instance->session['data'][':new:']['laravel_old_input']);

		Input::flash('except', array('name'));

		$this->assertEquals(array('age' => 25), Session::$instance->session['data'][':new:']['laravel_old_input']);
	}

	/**
	 * Test the Input::flush method.
	 *
	 * @group laravel
	 */
	public function testFlushMethodClearsFlashedInput()
	{
		$this->setSession();

		$input = array('name' => 'Taylor');
		Request::foundation()->request->add($input);

		Input::flash();

		$this->assertEquals($input, Session::$instance->session['data'][':new:']['laravel_old_input']);

		Input::flush();

		$this->assertEquals(array(), Session::$instance->session['data'][':new:']['laravel_old_input']);
	}

	/**
	 * Set the session payload instance.
	 */
	protected function setSession()
	{
		$driver = $this->getMock('Laravel\\Session\\Drivers\\Driver');

		Session::$instance = new Laravel\Session\Payload($driver);
	}

}