<?php

use Laravel\Session;
use Laravel\Session\Payload;

class DummyPayload {

	public function test() { return 'Foo'; }

}

class SessionTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the testing environment.
	 */
	public function setUp()
	{
		Config::set('application.key', 'foo');
		Session::$instance = null;
	}

	/**
	 * Tear down the testing environment.
	 */
	public function tearDown()
	{
		Config::set('application.key', '');
		Session::$instance = null;
	}

	/**
	 * Test the __callStatic method.
	 *
	 * @group laravel
	 */
	public function testPayloadCanBeCalledStaticly()
	{
		Session::$instance = new DummyPayload;
		$this->assertEquals('Foo', Session::test());
	}

	/**
	 * Test the Session::started method.
	 *
	 * @group laravel
	 */
	public function testStartedMethodIndicatesIfSessionIsStarted()
	{
		$this->assertFalse(Session::started());
		Session::$instance = 'foo';
		$this->assertTrue(Session::started());
	}

	/**
	 * Test the Payload::load method.
	 *
	 * @group laravel
	 */
	public function testLoadMethodCreatesNewSessionWithNullIDGiven()
	{
		$payload = $this->getPayload();
		$payload->load(null);
		$this->verifyNewSession($payload);
	}

	/**
	 * Test the Payload::load method.
	 *
	 * @group laravel
	 */
	public function testLoadMethodCreatesNewSessionWhenSessionIsExpired()
	{
		$payload = $this->getPayload();

		$session = $this->getSession();
		$session['last_activity'] = time() - 10000;

		$payload->driver->expects($this->any())
						->method('load')
						->will($this->returnValue($session));

		$payload->load('foo');

		$this->verifyNewSession($payload);
		$this->assertTrue($payload->session['id'] !== $session['id']);
	}

	/**
	 * Assert that a session is new.
	 *
	 * @param  Payload  $payload
	 * @return void
	 */
	protected function verifyNewSession($payload)
	{
		$this->assertFalse($payload->exists);
		$this->assertTrue(isset($payload->session['id']));
		$this->assertEquals(array(), $payload->session['data'][':new:']);
		$this->assertEquals(array(), $payload->session['data'][':old:']);
		$this->assertTrue(isset($payload->session['data'][Session::csrf_token]));
	}

	/**
	 * Test the Payload::load method.
	 *
	 * @group laravel
	 */
	public function testLoadMethodSetsValidSession()
	{
		$payload = $this->getPayload();

		$session = $this->getSession();

		$payload->driver->expects($this->any())
						->method('load')
						->will($this->returnValue($session));

		$payload->load('foo');

		$this->assertEquals($session, $payload->session);
	}

	/**
	 * Test the Payload::load method.
	 *
	 * @group laravel
	 */
	public function testLoadMethodSetsCSRFTokenIfDoesntExist()
	{
		$payload = $this->getPayload();

		$session = $this->getSession();

		unset($session['data']['csrf_token']);

		$payload->driver->expects($this->any())
						->method('load')
						->will($this->returnValue($session));

		$payload->load('foo');

		$this->assertEquals('foo', $payload->session['id']);
		$this->assertTrue(isset($payload->session['data']['csrf_token']));
	}

	/**
	 * Test the various data retrieval methods.
	 *
	 * @group laravel
	 */
	public function testSessionDataCanBeRetrievedProperly()
	{
		$payload = $this->getPayload();

		$payload->session = $this->getSession();

		$this->assertTrue($payload->has('name'));
		$this->assertEquals('Taylor', $payload->get('name'));
		$this->assertFalse($payload->has('foo'));
		$this->assertEquals('Default', $payload->get('foo', 'Default'));
		$this->assertTrue($payload->has('votes'));
		$this->assertEquals(10, $payload->get('votes'));
		$this->assertTrue($payload->has('state'));
		$this->assertEquals('AR', $payload->get('state'));
	}

	/**
	 * Test the various data manipulation methods.
	 *
	 * @group laravel
	 */
	public function testDataCanBeSetProperly()
	{
		$payload = $this->getPayload();

		$payload->session = $this->getSession();

		// Test the "put" and "flash" methods.
		$payload->put('name', 'Weldon');
		$this->assertEquals('Weldon', $payload->session['data']['name']);
		$payload->flash('language', 'php');
		$this->assertEquals('php', $payload->session['data'][':new:']['language']);

		// Test the "reflash" method.
		$payload->session['data'][':new:'] = array('name' => 'Taylor');
		$payload->session['data'][':old:'] = array('age' => 25);
		$payload->reflash();
		$this->assertEquals(array('name' => 'Taylor', 'age' => 25), $payload->session['data'][':new:']);

		// Test the "keep" method.
		$payload->session['data'][':new:'] = array();
		$payload->keep(array('age'));
		$this->assertEquals(25, $payload->session['data'][':new:']['age']);
	}

	/**
	 * Test the Payload::forget method.
	 *
	 * @group laravel
	 */
	public function testSessionDataCanBeForgotten()
	{
		$payload = $this->getPayload();

		$payload->session = $this->getSession();

		$this->assertTrue(isset($payload->session['data']['name']));
		$payload->forget('name');
		$this->assertFalse(isset($payload->session['data']['name']));
	}

	/**
	 * Test the Payload::flush method.
	 *
	 * @group laravel
	 */
	public function testFlushMaintainsTokenButDeletesEverythingElse()
	{
		$payload = $this->getPayload();

		$payload->session = $this->getSession();

		$this->assertTrue(isset($payload->session['data']['name']));
		$payload->flush();
		$this->assertFalse(isset($payload->session['data']['name']));
		$this->assertEquals('bar', $payload->session['data']['csrf_token']);
		$this->assertEquals(array(), $payload->session['data'][':new:']);
		$this->assertEquals(array(), $payload->session['data'][':old:']);
	}

	/**
	 * Test the Payload::regenerate method.
	 *
	 * @group laravel
	 */
	public function testRegenerateMethodSetsNewIDAndTurnsOffExistenceIndicator()
	{
		$payload = $this->getPayload();

		$payload->sesion = $this->getSession();
		$payload->exists = true;
		$payload->regenerate();

		$this->assertFalse($payload->exists);
		$this->assertTrue(strlen($payload->session['id']) == 40);
	}

	/**
	 * Test the Payload::token method.
	 *
	 * @group laravel
	 */
	public function testTokenMethodReturnsCSRFToken()
	{
		$payload = $this->getPayload();
		$payload->session = $this->getSession();

		$this->assertEquals('bar', $payload->token());
	}

	/**
	 * Test the Payload::save method.
	 *
	 * @group laravel
	 */
	public function testSaveMethodCorrectlyCallsDriver()
	{
		$payload = $this->getPayload();
		$session = $this->getSession();
		$payload->session = $session;
		$payload->exists = true;
		$config = Laravel\Config::get('session');

		$expect = $session;
		$expect['data'][':old:'] = $session['data'][':new:'];
		$expect['data'][':new:'] = array();

		$payload->driver->expects($this->once())
						->method('save')
						->with($this->equalTo($expect), $this->equalTo($config), $this->equalTo(true));

		$payload->save();

		$this->assertEquals($session['data'][':new:'], $payload->session['data'][':old:']);
	}

	/**
	 * Test the Payload::save method.
	 *
	 * @group laravel
	 */
	public function testSaveMethodSweepsIfSweeperAndOddsHitWithTimeGreaterThanThreshold()
	{
		Config::set('session.sweepage', array(100, 100));

		$payload = $this->getPayload();
		$payload->driver = $this->getMock('Laravel\\Session\\Drivers\\File', array('save', 'sweep'), array(null));
		$payload->session = $this->getSession();

		$expiration = time() - (Config::get('session.lifetime') * 60);

		// Here we set the time to the expected expiration minus 5 seconds, just to
		// allow plenty of room for PHP execution. In the next test, we'll do the
		// same thing except add 5 seconds to check that the time is between a
		// given window.
		$payload->driver->expects($this->once())
						->method('sweep')
						->with($this->greaterThan($expiration - 5));

		$payload->save();

		Config::set('session.sweepage', array(2, 100));
	}

	/**
	 * Test the Payload::save method.
	 *
	 * @group laravel
	 */
	public function testSaveMethodSweepsIfSweeperAndOddsHitWithTimeLessThanThreshold()
	{
		Config::set('session.sweepage', array(100, 100));

		$payload = $this->getPayload();
		$payload->driver = $this->getMock('Laravel\\Session\\Drivers\\File', array('save', 'sweep'), array(null));
		$payload->session = $this->getSession();

		$expiration = time() - (Config::get('session.lifetime') * 60);

		$payload->driver->expects($this->once())
						->method('sweep')
						->with($this->lessThan($expiration + 5));

		$payload->save();

		Config::set('session.sweepage', array(2, 100));
	}

	/**
	 * Test that the session sweeper is never called if not a sweeper.
	 *
	 * @group laravel
	 */
	public function testSweeperShouldntBeCalledIfDriverIsntSweeper()
	{
		Config::set('session.sweepage', array(100, 100));

		$payload = $this->getPayload();
		$payload->driver = $this->getMock('Laravel\\Session\\Drivers\\APC', array('save', 'sweep'), array(), '', false);
		$payload->session = $this->getSession();

		$payload->driver->expects($this->never())->method('sweep');

		$payload->save();

		Config::set('session.sweepage', array(2, 100));
	}

	/**
	 * Test the Payload::save method.
	 *
	 * @group laravel
	 */
	public function testSaveMethodSetsCookieWithCorrectValues()
	{
		$payload = $this->getPayload();
		$payload->session = $this->getSession();
		$payload->save();

		$this->assertTrue(isset(Cookie::$jar[Config::get('session.cookie')]));

		$cookie = Cookie::$jar[Config::get('session.cookie')];

		$this->assertEquals(sha1('foo'.Config::get('application.key')).'+foo', $cookie['value']);
		// Shouldn't be able to test this cause session.lifetime store number of minutes 
		// while cookie expiration store timestamp when it going to expired.
		// $this->assertEquals(Config::get('session.lifetime'), $cookie['expiration']);
		$this->assertEquals(Config::get('session.domain'), $cookie['domain']);
		$this->assertEquals(Config::get('session.path'), $cookie['path']);
		$this->assertEquals(Config::get('session.secure'), $cookie['secure']);
	}

	/**
	 * Test the Session::activity method.
	 *
	 * @group laravel
	 */
	public function testActivityMethodReturnsLastActivity()
	{
		$payload = $this->getPayload();
		$payload->session['last_activity'] = 10;
		$this->assertEquals(10, $payload->activity());
	}

	/**
	 * Get a session payload instance.
	 *
	 * @return Payload
	 */
	protected function getPayload()
	{
		return new Payload($this->getMockDriver());
	}

	/**
	 * Get a mock driver instance.
	 *
	 * @return Driver
	 */
	protected function getMockDriver()
	{
		$mock = $this->getMock('Laravel\\Session\\Drivers\\Driver', array('id', 'load', 'save', 'delete'));

		$mock->expects($this->any())->method('id')->will($this->returnValue(Str::random(40)));

		return $mock;
	}

	/**
	 * Get a dummy session.
	 *
	 * @return array
	 */
	protected function getSession()
	{
		return array(
			'id'            => 'foo',
			'last_activity' => time(),
			'data'          => array(
				'name'       => 'Taylor',
				'age'        => 25,
				'csrf_token' => 'bar',
				':new:'      => array(
						'votes' => 10,
				),
				':old:'      => array(
						'state' => 'AR',
				),
		));
	}

}