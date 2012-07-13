<?php

use Laravel\Str;
use Laravel\Auth;
use Laravel\Cookie;
use Laravel\Session;
use Laravel\Crypter;
use Laravel\Session\Payload;

class AuthTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup teh test environment.
	 */
	public function setUp()
	{
		$_SERVER['auth.login.stub'] = null;
		Cookie::$jar = array();
		Config::$items = array();
		Auth::driver()->user = null;
		Session::$instance = null;
		Config::set('database.default', 'sqlite');
	}

	/**
	 * Tear down the test environment.
	 */
	public function tearDown()
	{
		$_SERVER['auth.login.stub'] = null;
		Cookie::$jar = array();
		Config::$items = array();
		Auth::driver()->user = null;
		Session::$instance = null;
		Config::set('database.default', 'mysql');
	}

	/**
	 * Test the Auth::user method.
	 *
	 * @group laravel
	 */
	public function testUserMethodReturnsCurrentUser()
	{
		Auth::driver()->user = 'Taylor';

		$this->assertEquals('Taylor', Auth::user());
	}

	/**
	 * Test the Auth::check method.
	 *
	 * @group laravel
	 */
	public function testCheckMethodReturnsTrueWhenUserIsSet()
	{
		$this->assertTrue(AuthUserReturnsDummy::check());
	}

	/**
	 * Test the Auth::check method.
	 *
	 * @group laravel
	 */
	public function testCheckMethodReturnsFalseWhenNoUserIsSet()
	{
		$this->assertFalse(AuthUserReturnsNull::check());
	}

	/**
	 * Test the Auth::guest method.
	 *
	 * @group laravel
	 */
	public function testGuestReturnsTrueWhenNoUserIsSet()
	{
		$this->assertTrue(AuthUserReturnsNull::guest());
	}

	/**
	 * Test the Auth::guest method.
	 *
	 * @group laravel
	 */
	public function testGuestReturnsFalseWhenUserIsSet()
	{
		$this->assertFalse(AuthUserReturnsDummy::guest());
	}

	/**
	 * Test the Auth::user method.
	 *
	 * @group laravel
	 */
	public function testUserMethodReturnsNullWhenNoUserExistsAndNoRecallerExists()
	{
		Session::$instance = new Payload($this->getMock('Laravel\\Session\\Drivers\\Driver'));

		$this->assertNull(Auth::user());
	}

	/**
	 * Test the Auth::user method.
	 *
	 * @group laravel
	 */
	public function testUserReturnsUserByID()
	{
		Session::$instance = new Payload($this->getMock('Laravel\\Session\\Drivers\\Driver'));
		// FIXME: Not sure whether hard-coding the key is a good idea.
		Session::$instance->session['data']['laravel_auth_drivers_fluent_login'] = 1;

		$this->assertEquals('Taylor Otwell', Auth::user()->name);
	}

	/**
	 * Test the Auth::user method.
	 *
	 * @group laravel
	 */
	public function testNullReturnedWhenUserIDNotValidInteger()
	{
		Session::$instance = new Payload($this->getMock('Laravel\\Session\\Drivers\\Driver'));
		// FIXME: Not sure whether hard-coding the key is a good idea.
		Session::$instance->session['data']['laravel_auth_drivers_fluent_login'] = 'asdlkasd';

		$this->assertNull(Auth::user());
	}

	/**
	 * Test the Auth::recall method.
	 *
	 * @group laravel
	 */
	public function testUserCanBeRecalledViaCookie()
	{
		Session::$instance = new Payload($this->getMock('Laravel\\Session\\Drivers\\Driver'));

		$cookie = Crypter::encrypt('1|'.Str::random(40));
		Cookie::forever(Config::get('auth.cookie'), $cookie);

		$this->assertEquals('Taylor Otwell', AuthLoginStub::user()->name);
		$this->assertTrue(AuthLoginStub::user() === $_SERVER['auth.login.stub']['user']);
	}

	/**
	 * Test the Auth::attempt method.
	 *
	 * @group laravel
	 */
	public function testAttemptMethodReturnsFalseWhenCredentialsAreInvalid()
	{
		$this->assertFalse(Auth::attempt('foo', 'foo'));
		$this->assertFalse(Auth::attempt('foo', null));
		$this->assertFalse(Auth::attempt(null, null));
		$this->assertFalse(Auth::attempt('taylor', 'password'));
		$this->assertFalse(Auth::attempt('taylor', 232));
	}

	/**
	 * Test the Auth::attempt method.
	 *
	 * @group laravel
	 */
	public function testAttemptReturnsTrueWhenCredentialsAreCorrect()
	{
		$this->assertTrue(AuthLoginStub::attempt('taylor', 'password1'));
		$this->assertEquals('Taylor Otwell', $_SERVER['auth.login.stub']['user']->name);
		$this->assertFalse($_SERVER['auth.login.stub']['remember']);

		$this->assertTrue(AuthLoginStub::attempt('taylor', 'password1', true));
		$this->assertEquals('Taylor Otwell', $_SERVER['auth.login.stub']['user']->name);
		$this->assertTrue($_SERVER['auth.login.stub']['remember']);
	}

	/**
	 * Test Auth::login method.
	 *
	 * @group laravel
	 */
	public function testLoginMethodStoresUserKeyInSession()
	{
		Session::$instance = new Payload($this->getMock('Laravel\\Session\\Drivers\\Driver'));

		$user = new StdClass;
		$user->id = 10;
		Auth::login($user);
		// FIXME: Not sure whether hard-coding the key is a good idea.
		$user = Session::$instance->session['data']['laravel_auth_drivers_fluent_login'];
		$this->assertEquals(10, $user->id);

		Auth::login(5);
		$user = Session::$instance->session['data']['laravel_auth_drivers_fluent_login'];
		$this->assertEquals(5, $user);
	}

	/**
	 * Test the Auth::login method.
	 *
	 * @group laravel
	 */
	public function testLoginStoresRememberCookieWhenNeeded()
	{
		Session::$instance = new Payload($this->getMock('Laravel\\Session\\Drivers\\Driver'));

		// Set the session vars to make sure remember cookie uses them
		Config::set('session.path', 'foo');
		Config::set('session.domain', 'bar');
		Config::set('session.secure', true);

		Auth::login(10);
		$this->assertTrue(isset(Cookie::$jar[Config::get('auth.cookie')]));

		$cookie = Cookie::$jar[Config::get('auth.cookie')]['value'];
		$cookie = explode('|', Crypter::decrypt($cookie));
		$this->assertEquals(10, $cookie[0]);
		$this->assertEquals('foo', Cookie::$jar[Config::get('auth.cookie')]['path']);
		$this->assertEquals('bar', Cookie::$jar[Config::get('auth.cookie')]['domain']);
		$this->assertTrue(Cookie::$jar[Config::get('auth.cookie')]['secure']);
	}

	/**
	 * Test the Auth::logout method.
	 *
	 * @group laravel
	 */
	public function testLogoutMethodLogsOutUser()
	{
		Session::$instance = new Payload($this->getMock('Laravel\\Session\\Drivers\\Driver'));
		
		//$data = Session::$instance->session['data']['laravel_auth_drivers_fluent_login'] = 10;

		// FIXME: Restore some of these!
		//Config::set('auth.logout', function($user) { $_SERVER['auth.logout.stub'] = $user; });

		//Auth::$user = 'Taylor';
		Auth::logout();

		//$this->assertEquals('Taylor', $_SERVER['auth.logout.stub']);
		$this->assertNull(Auth::user());
		// FIXME: Not sure whether hard-coding the key is a good idea.
		$this->assertFalse(isset(Session::$instance->session['data']['laravel_auth_drivers_fluent_login']));
		$this->assertTrue(Cookie::$jar['laravel_auth_drivers_fluent_remember']['expiration'] < time());
	}

}

class AuthUserReturnsNull extends Laravel\Auth {

	public static function user() {}

}

class AuthUserReturnsDummy extends Laravel\Auth {

	public static function user() { return 'Taylor'; }

}

class AuthLoginStub extends Laravel\Auth {
	
	public static function login($user, $remember = false) 
	{
		$_SERVER['auth.login.stub'] = compact('user', 'remember');
	}

}