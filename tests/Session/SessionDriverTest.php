<?php

use Laravel\IoC;

class SessionDriverTest extends PHPUnit_Framework_TestCase {

	public function testStartMethodStartsNewSessionWhenNullIDGiven()
	{
		$driver = IoC::resolve('laravel.session.file');
		
		$driver->start(IoC::resolve('laravel.config'), null);
		
		$this->assertTrue(is_string($driver->session['id']));
		$this->assertEquals(strlen($driver->session['id']), 40);
		$this->assertTrue(is_array($driver->session['data']));
		$this->assertEquals(strlen($driver->session['data']['csrf_token']), 16);
	}


	public function testStartMethodCallsLoadWhenIDIsGiven()
	{
		$mock = $this->getFileDriverMock();

		$mock->expects($this->once())
							->method('load')
							->with($this->equalTo('something'));

		$mock->start(IoC::resolve('laravel.config'), 'something');
	}


	public function testSessionIsLoadedWhenIDIsValid()
	{
		$mock = $this->getFileDriverMock();

		$time = time();

		$session = array('id' => 'something', 'last_activity' => $time, 'data' => array('name' => 'Taylor', 'csrf_token' => 'token'));

		$this->setMockLoadExpectations($mock, $session);

		$mock->start(IoC::resolve('laravel.config'), 'something');

		$this->assertEquals($mock->session['id'], 'something');
		$this->assertEquals($mock->session['last_activity'], $time);
		$this->assertEquals($mock->session['data'], array('name' => 'Taylor', 'csrf_token' => 'token'));
	}


	public function testSessionIsRestartedWhenLoadedSessionIsExpired()
	{
		$mock = $this->getFileDriverMock();
		
		$time = new DateTime('2009-01-01');
		$time = $time->getTimestamp();

		$session = array('id' => 'something', 'last_activity' => $time, 'data' => array('name' => 'Taylor'));

		$this->setMockLoadExpectations($mock, $session);

		$mock->start(IoC::resolve('laravel.config'), 'something');

		$this->assertEquals(strlen($mock->session['id']), 40);
		$this->assertFalse(isset($mock->session['data']['name']));
		$this->assertTrue(isset($mock->session['data']['csrf_token']));
	}


	public function testHasMethodIndicatesIfItemExistsInSession()
	{
		$mock = $this->getSessionDriverWithData();

		$this->assertTrue($mock->has('name'));
		$this->assertFalse($mock->has('test'));
	}


	public function testGetMethodGetsItemsFromTheSession()
	{
		$mock = $this->getSessionDriverWithData();

		$this->assertNull($mock->get('test'));
		$this->assertEquals($mock->get('name'), 'Taylor');
		$this->assertEquals($mock->name, 'Taylor');
		$this->assertEquals($mock->get('test', 'Taylor'), 'Taylor');
		$this->assertEquals($mock->get('test', function() {return 'Taylor';}), 'Taylor');

		$mock->session['data'][':old:test1'] = 'test1';
		$mock->session['data'][':new:test2'] = 'test2';

		$this->assertEquals($mock->get('test1'), 'test1');
		$this->assertEquals($mock->get('test2'), 'test2');
	}


	public function testPutMethodPutsItemsInTheSession()
	{
		$mock = $this->getSessionDriverWithData();

		$mock->put('name', 'Tony');
		$mock->age = 30;
		
		$this->assertEquals($mock->session['data']['name'], 'Tony');
		$this->assertEquals($mock->session['data']['age'], 30);
	}


	public function testFlashMethodPutsItemsInFlashData()
	{
		$mock = $this->getSessionDriverWithData();
		
		$mock->flash('name', 'James');
		
		$this->assertEquals($mock->session['data'][':new:name'], 'James');	
	}


	public function testKeepMethodRejuvenatesFlashData()
	{
		$mock = $this->getSessionDriverWithData();
		
		$mock->session['data'][':old:test'] = 'test';
		$mock->keep('test');
		
		$this->assertFalse(isset($mock->session['data'][':old:test']));
		$this->assertEquals($mock->session['data'][':new:test'], 'test');
	}


	public function testKeepMethodRejuvenatesAllFlashDataInArray()
	{
		$mock = $this->getSessionDriverWithData();

		$mock->session['data'][':old:test1'] = 'test1';
		$mock->session['data'][':old:test2'] = 'test2';

		$mock->keep(array('test1', 'test2'));

		$this->assertFalse(isset($mock->session['data'][':old:test1']));
		$this->assertFalse(isset($mock->session['data'][':old:test2']));
		$this->assertEquals($mock->session['data'][':new:test1'], 'test1');
		$this->assertEquals($mock->session['data'][':new:test2'], 'test2');
	}


	public function testReflashMethodRejuvenatesAllFlashData()
	{
		$mock = $this->getSessionDriverWithData();

		$mock->session['data'][':old:test1'] = 'test1';
		$mock->session['data'][':old:test2'] = 'test2';

		$mock->reflash();

		$this->assertFalse(isset($mock->session['data'][':old:test1']));
		$this->assertFalse(isset($mock->session['data'][':old:test2']));
		$this->assertEquals($mock->session['data'][':new:test1'], 'test1');
		$this->assertEquals($mock->session['data'][':new:test2'], 'test2');
	}


	public function testForgetMethodRemovesDataFromSession()
	{
		$mock = $this->getSessionDriverWithData();
		
		$mock->forget('name');

		$this->assertFalse(isset($mock->session['data']['name']));
	}


	public function testFlushMethodsClearsEntireSessionData()
	{
		$mock = $this->getSessionDriverWithData();

		$mock->flush();

		$this->assertEquals(count($mock->session['data']), 0);
	}


	public function testRegenerateMethodDeletesSessionAndResetsID()
	{
		$mock = $this->getMock('Laravel\\Session\\Drivers\\File', array('load', 'delete'), $this->getFileDriverConstructor());

		$this->setMockLoadExpectations($mock, $this->getDummySession());

		$mock->expects($this->once())
							->method('delete')
							->with($this->equalTo('something'));

		$mock->start(IoC::resolve('laravel.config'), 'something');

		$mock->regenerate();

		$this->assertEquals(strlen($mock->session['id']), 40);
	}


	public function testCloseMethodFlashesOldInputData()
	{
		$mock = $this->getMock('Laravel\\Session\\Drivers\\File', array('save'), $this->getFileDriverConstructor());

		$this->setMockLoadExpectations($mock, $this->getDummySession());

		$mock->start(IoC::resolve('laravel.config'), 'something');

		$mock->close(new InputStub, time());

		$this->assertEquals($mock->session['data'][':old:laravel_old_input'], array('name' => 'Taylor'));
	}


	public function testCloseMethodAgesFlashData()
	{
		$mock = $this->getSessionDriverWithData();

		$mock->session['data'][':old:old'] = 'old';
		$mock->flash('flash', 'flash');

		$mock->close(new InputStub, time());

		$this->assertFalse(isset($mock->session['data'][':old:old']));
		$this->assertFalse(isset($mock->session['data'][':new:flash']));
		$this->assertEquals($mock->session['data'][':old:flash'], 'flash');
	}


	public function testCloseMethodSavesSession()
	{
		$mock = $this->getMock('Laravel\\Session\\Drivers\\File', array('load', 'save', 'sweep'), $this->getFileDriverConstructor());

		$session = $this->getDummySession();

		$session['data']['csrf_token'] = 'token';

		$this->setMockLoadExpectations($mock, $session);

		$expect = $session;

		Laravel\Arr::set($expect, 'data.:old:laravel_old_input', array('name' => 'Taylor'));

		$mock->expects($this->once())
							->method('save')
							->with($this->equalTo($expect));

		$mock->start(IoC::resolve('laravel.config'), 'something');

		$mock->close(new InputStub, $mock->session['last_activity']);
	}


	/**
	 * @dataProvider cookieMethodProvider
	 */
	public function testCookieMethodWritesCookie($expire_on_close, $minutes)
	{
		$mock = $this->getSessionDriverWithData();

		$config = IoC::resolve('laravel.config');

		$config->set('session.expire_on_close', $expire_on_close);

		$mock->start($config, 'something');

		$cookieMock = $this->getMock('Laravel\\Cookie', array('put'), array(array()));

		$cookieMock->expects($this->once())
						->method('put')
						->with('laravel_session', 'something', $minutes, $config->get('session.path'), $config->get('session.domain'));

		$mock->cookie($cookieMock);
	}

	// -----------------------------------------------------------------------------------
	// Utility Methods & Providers
	// -----------------------------------------------------------------------------------

	public function getSessionDriverWithData()
	{
		$mock = $this->getFileDriverMock();

		$this->setMockLoadExpectations($mock, $this->getDummySession());

		$mock->start(IoC::resolve('laravel.config'), 'something');

		return $mock;
	}


	private function getFileDriverMock()
	{
		return $this->getMock('Laravel\\Session\\Drivers\\File', array('load', 'save'), $this->getFileDriverConstructor());
	}


	private function getFileDriverConstructor()
	{
		return array(IoC::resolve('laravel.file'), null);
	}


	private function setMockLoadExpectations($mock, $session)
	{
		$mock->expects($this->any())
							->method('load')
							->will($this->returnValue($session));
	}


	private function getDummySession()
	{
		return array('id' => 'something', 'last_activity' => time(), 'data' => array('name' => 'Taylor'));
	}


	public function cookieMethodProvider()
	{
		return array(
			array(false, 60),
			array(true, 0),
		);
	}

}

// -----------------------------------------------------------------------------------
// Stub Classes
// -----------------------------------------------------------------------------------

class InputStub extends Laravel\Input {

	public function __construct() {}

	public function get($key = null, $default = null)
	{
		return array('name' => 'Taylor');
	}

}

class CookieStub extends Laravel\Cookie {

	public function put() {}

}