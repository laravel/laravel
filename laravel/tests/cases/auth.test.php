<?php

use Symfony\Component\HttpFoundation\LaravelRequest as RequestFoundation;

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
	 * Set one of the $_SERVER variables.
	 *
	 * @param string  $key
	 * @param string  $value
	 */
	protected function setServerVar($key, $value)
	{
		$_SERVER[$key] = $value;

		$this->restartRequest();
	}

	/**
	 * Reinitialize the global request.
	 * 
	 * @return void
	 */
	protected function restartRequest()
	{
		// FIXME: Ugly hack, but old contents from previous requests seem to
		// trip up the Foundation class.
		$_FILES = array();

		Request::$foundation = RequestFoundation::createFromGlobals();
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
		$auth = new AuthUserReturnsDummy;

		$this->assertTrue($auth->check());
	}

	/**
	 * Test the Auth::check method.
	 *
	 * @group laravel
	 */
	public function testCheckMethodReturnsFalseWhenNoUserIsSet()
	{
		$auth = new AuthUserReturnsNull;

		$this->assertFalse($auth->check());
	}

	/**
	 * Test the Auth::guest method.
	 *
	 * @group laravel
	 */
	public function testGuestReturnsTrueWhenNoUserIsSet()
	{
		$auth = new AuthUserReturnsNull;

		$this->assertTrue($auth->guest());
	}

	/**
	 * Test the Auth::guest method.
	 *
	 * @group laravel
	 */
	public function testGuestReturnsFalseWhenUserIsSet()
	{
		$auth = new AuthUserReturnsDummy;
		
		$this->assertFalse($auth->guest());
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
		
		Auth::login(1);

		$this->assertEquals('Taylor Otwell', Auth::user()->name);

		Auth::logout();
	}

	/**
	 * Test the Auth::user method.
	 *
	 * @group laravel
	 */
	public function testNullReturnedWhenUserIDNotValidInteger()
	{
		Session::$instance = new Payload($this->getMock('Laravel\\Session\\Drivers\\Driver'));
		
		Auth::login('asdlkasd');

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
		Cookie::forever('authloginstub_remember', $cookie);

		$auth = new AuthLoginStub;

		$this->assertEquals('Taylor Otwell', $auth->user()->name);
		
		$this->assertTrue($auth->user()->id === $_SERVER['auth.login.stub']['user']);
	}

	/**
	 * Test the Auth::attempt method.
	 *
	 * @group laravel
	 */
	public function testAttemptMethodReturnsFalseWhenCredentialsAreInvalid()
	{
		$this->assertFalse(Auth::attempt(array('username' => 'foo', 'password' => 'foo')));
		$this->assertFalse(Auth::attempt(array('username' => 'foo', 'password' => null)));
		$this->assertFalse(Auth::attempt(array('username' => null, 'password' => null)));
		$this->assertFalse(Auth::attempt(array('username' => 'taylor', 'password' => 'password')));
		$this->assertFalse(Auth::attempt(array('username' => 'taylor', 'password' => 232)));
	}

	/**
	 * Test the Auth::attempt method.
	 *
	 * @group laravel
	 */
	public function testAttemptReturnsTrueWhenCredentialsAreCorrect()
	{
		Session::$instance = new Payload($this->getMock('Laravel\\Session\\Drivers\\Driver'));

		$auth = new AuthLoginStub;

		$this->assertTrue($auth->attempt(array('username' => 'taylor', 'password' => 'password1')));
		$this->assertEquals('1', $_SERVER['auth.login.stub']['user']);
		$this->assertFalse($_SERVER['auth.login.stub']['remember']);

		$auth_secure = new AuthLoginStub;

		$this->assertTrue($auth_secure->attempt(array('username' => 'taylor', 'password' => 'password1', 'remember' => true)));
		$this->assertEquals('1', $_SERVER['auth.login.stub']['user']);
		$this->assertTrue($_SERVER['auth.login.stub']['remember']);

		$auth_secure->logout();
		$auth->logout();
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


		Auth::logout();

		Auth::login(5);
		$user = Session::$instance->session['data']['laravel_auth_drivers_fluent_login'];
		$this->assertEquals(5, $user);
		Auth::logout(5);
	}

	/**
	 * Test the Auth::login method.
	 *
	 * @group laravel
	 */
	public function testLoginStoresRememberCookieWhenNeeded()
	{
		Session::$instance = new Payload($this->getMock('Laravel\\Session\\Drivers\\Driver'));

		$this->setServerVar('HTTPS', 'on');

		// Set the session vars to make sure remember cookie uses them
		Config::set('session.path', 'foo');
		Config::set('session.domain', 'bar');
		Config::set('session.secure', true);

		Auth::login(1, true);

		$this->assertTrue(isset(Cookie::$jar['laravel_auth_drivers_fluent_remember']));

		$cookie = Cookie::$jar['laravel_auth_drivers_fluent_remember']['value'];
		$cookie = explode('|', Crypter::decrypt($cookie));
		$this->assertEquals(1, $cookie[0]);
		$this->assertEquals('foo', Cookie::$jar['laravel_auth_drivers_fluent_remember']['path']);
		$this->assertEquals('bar', Cookie::$jar['laravel_auth_drivers_fluent_remember']['domain']);
		$this->assertTrue(Cookie::$jar['laravel_auth_drivers_fluent_remember']['secure']);

		Auth::logout();

		$this->setServerVar('HTTPS', 'off');
	}

	/**
	 * Test the Auth::logout method.
	 *
	 * @group laravel
	 */
	public function testLogoutMethodLogsOutUser()
	{
		Session::$instance = new Payload($this->getMock('Laravel\\Session\\Drivers\\Driver'));
		
		$data = Session::$instance->session['data']['laravel_auth_drivers_fluent_login'] = 1;

		Auth::logout();

		$this->assertNull(Auth::user());

		$this->assertFalse(isset(Session::$instance->session['data']['laravel_auth_drivers_fluent_login']));
		$this->assertTrue(Cookie::$jar['laravel_auth_drivers_fluent_remember']['expiration'] < time());
	}

}

class AuthUserReturnsNull extends Laravel\Auth\Drivers\Driver {

	public function user() { return null; }

	public function retrieve($id) { return null; }

	public function attempt($arguments = array()) { return null; }

}

class AuthUserReturnsDummy extends Laravel\Auth\Drivers\Driver {

	public function user() { return 'Taylor'; }

	public function retrieve($id) { return null; }

	public function attempt($arguments = array()) 
	{
		return $this->login($arguments['username']); 
	}

}

class AuthLoginStub extends Laravel\Auth\Drivers\Fluent {
	
	public function login($user, $remember = false) 
	{
		if (is_null($remember)) $remember = false;

		$_SERVER['auth.login.stub'] = compact('user', 'remember');

		return parent::login($user, $remember);
	}

	public function logout()
	{
		parent::logout();
	}

	public function retrieve($id)
	{
		$user = parent::retrieve($id);

		$_SERVER['auth.login.stub'] = array(
			'user'     => $user->id,
			'remember' => false,
		);

		return $user;
	}

}