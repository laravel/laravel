<?php

use Laravel\IoC;
use Laravel\Session\Manager;

class SessionManagerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider mockProvider
	 */
	public function test_session_manager_calls_transporter_get($driver, $transporter)
	{
		$transporter->expects($this->once())->method('get');
		$manager = new Manager($driver, $transporter);
		$manager->payload($this->getConfig());
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_session_manager_calls_driver_load_with_session_id($driver, $transporter)
	{
		$transporter->expects($this->any())->method('get')->will($this->returnValue('something'));
		$driver->expects($this->once())->method('load')->with($this->equalTo('something'));
		$manager = new Manager($driver, $transporter);
		$manager->payload($this->getConfig());
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_session_manager_returns_payload_when_found($driver, $transporter)
	{
		$this->setDriverExpectation($driver, 'load', $this->getDummySession());
		$manager = new Manager($driver, $transporter);
		$payload = $manager->payload($this->getConfig());
		$this->assertInstanceOf('Laravel\\Session\\Payload', $payload);
		$this->assertEquals($payload->session, $this->getDummySession());
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_session_manager_creates_new_session_when_session_is_null($driver, $transporter)
	{
		$this->setDriverExpectation($driver, 'load', null);
		$manager = new Manager($driver, $transporter);
		$payload = $manager->payload($this->getConfig());
		$this->assertInstanceOf('Laravel\\Session\\Payload', $payload);
		$this->assertEquals(strlen($payload->session['id']), 40);
		$this->assertTrue(is_array($payload->session['data']));
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_session_manager_creates_new_session_when_session_is_expired($driver, $transporter)
	{
		$dateTime = new DateTime('1970-01-01');
		$this->setDriverExpectation($driver, 'load', array('last_activity' => $dateTime->getTimestamp()));
		$manager = new Manager($driver, $transporter);
		$payload = $manager->payload($this->getConfig());
		$this->assertInstanceOf('Laravel\\Session\\Payload', $payload);
		$this->assertEquals(strlen($payload->session['id']), 40);
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_session_manager_sets_csrf_token_if_one_is_not_present($driver, $transporter)
	{
		$session = $this->getDummySession();
		unset($session['data']['csrf_token']);
		$this->setDriverExpectation($driver, 'load', $session);
		$manager = new Manager($driver, $transporter);
		$payload = $manager->payload($this->getConfig());
		$this->assertTrue(isset($payload->session['data']['csrf_token']));
		$this->assertEquals(strlen($payload->session['data']['csrf_token']), 16);
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_close_method_calls_driver_and_transporter($driver, $transporter)
	{
		$driver->expects($this->any())->method('load')->will($this->returnValue($this->getDummySession()));
		$manager = new Manager($driver, $transporter);
		$payload = $this->getMock('Laravel\\Session\\Payload', array('age'), array(array('id' => 'something')));
		$payload->expects($this->any())->method('age')->will($this->returnValue('something'));
		$driver->expects($this->once())->method('save')->with('something', $this->getConfig());
		$transporter->expects($this->once())->method('put')->with('something', $this->getConfig());
		$manager->close($payload, $this->getConfig());
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_close_method_calls_sweep_when_driver_is_sweeper($driver, $transporter)
	{
		$driver = $this->getMock('SweeperStub', array('sweep'));
		$driver->expects($this->once())->method('sweep');
		$manager = new Manager($driver, $transporter);
		$config = $this->getConfig();
		$config['sweepage'] = array(100, 100);
		$manager->close(new Laravel\Session\Payload($this->getDummySession()), $config);
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_close_method_doesnt_call_sweep_when_driver_isnt_sweeper($driver, $transporter)
	{
		$driver = $this->getMock('Laravel\\Session\\Drivers\\Driver', array('sweep', 'load', 'save', 'delete'));
		$driver->expects($this->never())->method('sweep');
		$manager = new Manager($driver, $transporter);
		$config = $this->getConfig();
		$config['sweepage'] = array(100, 100);
		$manager->close(new Laravel\Session\Payload($this->getDummySession()), $config);
	}

	// ---------------------------------------------------------------------
	// Providers
	// ---------------------------------------------------------------------

	public function mockProvider()
	{
		return array(array($this->getMockDriver(), $this->getMockTransporter()));
	}

	// ---------------------------------------------------------------------
	// Support Functions
	// ---------------------------------------------------------------------

	private function setDriverExpectation($mock, $method, $session)
	{
		$mock->expects($this->any())
						->method($method)
						->will($this->returnValue($session));
	}

	private function getMockDriver()
	{
		return $this->getMock('Laravel\\Session\\Drivers\\Driver');
	}

	private function getMockTransporter()
	{
		return $this->getMock('Laravel\\Session\\Transporters\\Transporter', array('get', 'put'));
	}

	private function getDummySession()
	{
		return array(
			'id'            => 'something',
			'last_activity' => time(),
			'data'          => array(
				'name'       => 'Taylor',
				'csrf_token' => 'token'
		));
	}

	private function getConfig()
	{
		return Laravel\Config::get('session');
	}

}

// ---------------------------------------------------------------------
// Stubs
// ---------------------------------------------------------------------

class SweeperStub implements Laravel\Session\Drivers\Driver, Laravel\Session\Drivers\Sweeper {

	public function load($id) {}
	public function save($session, $config, $exists) {}
	public function delete($id) {}
	public function sweep($expiration) {}

}