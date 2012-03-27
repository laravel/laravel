<?php

use Laravel\Routing\Router;

class RedirectTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Config::set('session.driver', 'foo');
		Router::$routes = array();
		Router::$names = array();
		Config::set('application.url', 'http://localhost');
		Config::set('application.index', '');
	}

	/**
	 * Destroy the test environment.
	 */
	public function tearDown()
	{
		Input::$input = array();
		Config::set('session.driver', '');
		Router::$routes = array();
		Router::$names = array();
		Config::set('application.url', '');
		Config::set('application.index', 'index.php');
		Session::$instance = null;
	}

	/**
	 * Test the Redirect::to method.
	 *
	 * @group laravel
	 */
	public function testSimpleRedirectSetsCorrectHeaders()
	{
		$redirect = Redirect::to('user/profile');

		$this->assertEquals(302, $redirect->status);
		$this->assertEquals('http://localhost/user/profile', $redirect->headers['location']);

		$redirect = Redirect::to('user/profile', 301, true);

		$this->assertEquals(301, $redirect->status);
		$this->assertEquals('https://localhost/user/profile', $redirect->headers['location']);

		$redirect = Redirect::to_secure('user/profile', 301);

		$this->assertEquals(301, $redirect->status);
		$this->assertEquals('https://localhost/user/profile', $redirect->headers['location']);
	}

	/**
	 * Test the Redirect::to_route method.
	 *
	 * @group laravel
	 */
	public function testRedirectsCanBeGeneratedForNamedRoutes()
	{
		Route::get('redirect', array('as' => 'redirect'));
		Route::get('redirect/(:any)/(:any)', array('as' => 'redirect-2'));
		Route::get('secure/redirect', array('https' => true, 'as' => 'redirect-3'));

		$this->assertEquals(301, Redirect::to_route('redirect', array(), 301, true)->status);
		$this->assertEquals('http://localhost/redirect', Redirect::to_route('redirect')->headers['location']);
		$this->assertEquals('https://localhost/secure/redirect', Redirect::to_route('redirect-3', array(), 302)->headers['location']);
		$this->assertEquals('http://localhost/redirect/1/2', Redirect::to_route('redirect-2', array('1', '2'))->headers['location']);
	}

	/**
	 * Test the Redirect::with method.
	 *
	 * @group laravel
	 */
	public function testWithMethodFlashesItemToSession()
	{
		$this->setSession();

		$redirect = Redirect::to('')->with('name', 'Taylor');

		$this->assertEquals('Taylor', Session::$instance->session['data'][':new:']['name']);
	}

	/**
	 * Test the Redirect::with_input function.
	 *
	 * @group laravel
	 */
	public function testWithInputMethodFlashesInputToTheSession()
	{
		$this->setSession();

		Input::$input = $input = array('name' => 'Taylor', 'age' => 25);

		$redirect = Redirect::to('')->with_input();

		$this->assertEquals($input, Session::$instance->session['data'][':new:']['laravel_old_input']);

		$redirect = Redirect::to('')->with_input('only', array('name'));

		$this->assertEquals(array('name' => 'Taylor'), Session::$instance->session['data'][':new:']['laravel_old_input']);

		$redirect = Redirect::to('')->with_input('except', array('name'));

		$this->assertEquals(array('age' => 25), Session::$instance->session['data'][':new:']['laravel_old_input']);
	}

	/**
	 * Test the Redirect::with_errors method.
	 *
	 * @group laravel
	 */
	public function testWithErrorsFlashesErrorsToTheSession()
	{
		$this->setSession();

		Redirect::to('')->with_errors(array('name' => 'Taylor'));

		$this->assertEquals(array('name' => 'Taylor'), Session::$instance->session['data'][':new:']['errors']);

		$validator = Validator::make(array(), array());
		$validator->errors = array('name' => 'Taylor');

		Redirect::to('')->with_errors($validator);

		$this->assertEquals(array('name' => 'Taylor'), Session::$instance->session['data'][':new:']['errors']);
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