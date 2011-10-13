<?php

use Laravel\IoC;
use Laravel\Config;
use Laravel\Session\Manager;

class SessionManagerTest extends PHPUnit_Framework_TestCase {

	public function setUp()
	{
		Manager::$session = array();
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_session_manager_calls_transporter_get($driver, $transporter)
	{
		$transporter->expects($this->once())->method('get');

		Manager::start($driver, $transporter);
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_session_manager_calls_driver_load_with_session_id($driver, $transporter)
	{
		$transporter->expects($this->any())
                                    ->method('get')
                                    ->will($this->returnValue('something'));

		$driver->expects($this->once())
                                    ->method('load')
                                    ->with($this->equalTo('something'));

		Manager::start($driver, $transporter);
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_session_manager_returns_payload_when_found($driver, $transporter)
	{
		$this->setDriverExpectation($driver, 'load', $this->getDummySession());

		Manager::start($driver, $transporter);

		$this->assertEquals(Manager::$session, $this->getDummySession());
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_session_manager_creates_new_session_when_session_is_null($driver, $transporter)
	{
		$this->setDriverExpectation($driver, 'load', null);

		Manager::start($driver, $transporter);

		$this->assertTrue(is_array(Manager::$session['data']));
		$this->assertEquals(strlen(Manager::$session['id']), 40);
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_session_manager_creates_new_session_when_session_is_expired($driver, $transporter)
	{
		$dateTime = new DateTime('1970-01-01');

		$this->setDriverExpectation($driver, 'load', array('last_activity' => $dateTime->getTimestamp()));

		Manager::start($driver, $transporter);

		$this->assertEquals(strlen(Manager::$session['id']), 40);
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_session_manager_sets_csrf_token_if_one_is_not_present($driver, $transporter)
	{
		$session = $this->getDummySession();
		unset($session['data']['csrf_token']);

		$this->setDriverExpectation($driver, 'load', $session);

		Manager::start($driver, $transporter);

		$this->assertTrue(isset(Manager::$session['data']['csrf_token']));
		$this->assertEquals(strlen(Manager::$session['data']['csrf_token']), 16);
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_close_method_calls_driver_and_transporter($driver, $transporter)
	{
		$driver->expects($this->any())
                                 ->method('load')
                                 ->will($this->returnValue($this->getDummySession()));

		Manager::start($driver, $transporter);

		$driver->expects($this->once())
                                 ->method('save');

		$transporter->expects($this->once())
                                 ->method('put');

		Manager::close($driver, $transporter);
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_close_method_calls_sweep_when_driver_is_sweeper($driver, $transporter)
	{
		$driver = $this->getMock('SweeperStub', array('sweep'));

		$driver->expects($this->once())->method('sweep');

		Manager::start($driver, $transporter);

		Config::$items['session']['sweepage'] = array(100, 100);

		Manager::close($driver, $transporter);
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_close_method_doesnt_call_sweep_when_driver_isnt_sweeper($driver, $transporter)
	{
		$driver = $this->getMock('Laravel\\Session\\Drivers\\Driver', array('sweep', 'load', 'save', 'delete'));

		$driver->expects($this->never())->method('sweep');

		Manager::start($driver, $transporter);

		Config::$items['session']['sweepage'] = array(100, 100);

		Manager::close($driver, $transporter);
	}

	public function test_has_method_indicates_if_item_exists_in_payload()
	{
		Manager::$session = $this->getDummyData();

		$this->assertTrue(Manager::has('name'));
		$this->assertTrue(Manager::has('age'));
		$this->assertTrue(Manager::has('gender'));
		$this->assertFalse(Manager::has('something'));
		$this->assertFalse(Manager::has('id'));
		$this->assertFalse(Manager::has('last_activity'));
	}

	public function test_get_method_returns_item_from_payload()
	{
		Manager::$session = $this->getDummyData();

		$this->assertEquals(Manager::get('name'), 'Taylor');
		$this->assertEquals(Manager::get('age'), 25);
		$this->assertEquals(Manager::get('gender'), 'male');
	}

	public function test_get_method_returns_default_when_item_doesnt_exist()
	{
		Manager::$session = $this->getDummyData();

		$this->assertNull(Manager::get('something'));
		$this->assertEquals('Taylor', Manager::get('something', 'Taylor'));
		$this->assertEquals('Taylor', Manager::get('something', function() {return 'Taylor';}));
	}

	public function test_put_method_adds_to_payload()
	{
		Manager::$session = $this->getDummyData();

		Manager::put('name', 'Weldon');
		Manager::put('workmate', 'Joe');

		$this->assertEquals(Manager::$session['data']['name'], 'Weldon');
		$this->assertEquals(Manager::$session['data']['workmate'], 'Joe');
	}

	public function test_flash_method_puts_item_in_flash_data()
	{
		Manager::$session = array();

		Manager::flash('name', 'Taylor');

		$this->assertEquals(Manager::$session['data'][':new:name'], 'Taylor');
	}

	public function test_reflash_keeps_all_session_data()
	{
		Manager::$session = array('data' => array(':old:name' => 'Taylor', ':old:age' => 25));

		Manager::reflash();

		$this->assertTrue(isset(Manager::$session['data'][':new:name']));
		$this->assertTrue(isset(Manager::$session['data'][':new:age']));
		$this->assertFalse(isset(Manager::$session['data'][':old:name']));
		$this->assertFalse(isset(Manager::$session['data'][':old:age']));
	}

	public function test_keep_method_keeps_specified_session_data()
	{
		Manager::$session = array('data' => array(':old:name' => 'Taylor', ':old:age' => 25));

		Manager::keep('name');

		$this->assertTrue(isset(Manager::$session['data'][':new:name']));
		$this->assertFalse(isset(Manager::$session['data'][':old:name']));
		
		Manager::$session = array('data' => array(':old:name' => 'Taylor', ':old:age' => 25));

		Manager::keep(array('name', 'age'));

		$this->assertTrue(isset(Manager::$session['data'][':new:name']));
		$this->assertTrue(isset(Manager::$session['data'][':new:age']));
		$this->assertFalse(isset(Manager::$session['data'][':old:name']));
		$this->assertFalse(isset(Manager::$session['data'][':old:age']));
	}

	public function test_flush_method_clears_payload_data()
	{
		Manager::$session = array('data' => array('name' => 'Taylor'));

		Manager::flush();

		$this->assertEquals(count(Manager::$session['data']), 0);
	}

	public function test_regenerate_session_sets_new_session_id()
	{
		Manager::$session = array('id' => 'something');

		Manager::regenerate();

		$this->assertTrue(Manager::$regenerated);
		$this->assertEquals(strlen(Manager::$session['id']), 40);
	}

	public function test_age_method_sets_last_activity_time()
	{
		$data = $this->getDummyData();
		unset($data['last_activity']);

		Manager::$session = $data;
		Manager::age();

		$this->assertTrue(isset(Manager::$session['last_activity']));
	}

	public function test_age_method_ages_all_flash_data()
	{
		Manager::$session = $this->getDummyData();

		Manager::age();

		$this->assertTrue(isset(Manager::$session['data'][':old:age']));
		$this->assertFalse(isset(Manager::$session['data'][':old:gender']));
	}

	public function test_age_method_returns_session_array()
	{
		Manager::$session = $this->getDummyData();

		$age = Manager::age();

		$this->assertEquals($age['id'], 'something');
	}

	// ---------------------------------------------------------------------
	// Support Functions
	// ---------------------------------------------------------------------

	public function getDummyData()
	{
		return array('id' => 'something', 'last_activity' => time(), 'data' => array(
				'name'        => 'Taylor',
				':new:age'    => 25,
				':old:gender' => 'male',
				'state'       => 'Oregon',
		));
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
		return Config::$items['session'];
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