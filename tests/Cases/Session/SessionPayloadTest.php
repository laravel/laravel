<?php

use Laravel\Session\Payload;

class SessionPayloadTest extends PHPUnit_Framework_TestCase {

	public function test_has_method_indicates_if_item_exists_in_payload()
	{
		$payload = new Payload($this->getDummyData());
		$this->assertTrue($payload->has('name'));
		$this->assertTrue($payload->has('age'));
		$this->assertTrue($payload->has('gender'));
		$this->assertFalse($payload->has('something'));
		$this->assertFalse($payload->has('id'));
		$this->assertFalse($payload->has('last_activity'));
	}

	public function test_get_method_returns_item_from_payload()
	{
		$payload = new Payload($this->getDummyData());
		$this->assertEquals($payload->get('name'), 'Taylor');
		$this->assertEquals($payload->get('age'), 25);
		$this->assertEquals($payload->get('gender'), 'male');
	}

	public function test_get_method_returns_default_when_item_doesnt_exist()
	{
		$payload = new Payload($this->getDummyData());
		$this->assertNull($payload->get('something'));
		$this->assertEquals('Taylor', $payload->get('something', 'Taylor'));
		$this->assertEquals('Taylor', $payload->get('something', function() {return 'Taylor';}));
	}

	public function test_put_method_adds_to_payload()
	{
		$payload = new Payload($this->getDummyData());
		$payload->put('name', 'Weldon');
		$payload->put('workmate', 'Joe');
		$this->assertEquals($payload->session['data']['name'], 'Weldon');
		$this->assertEquals($payload->session['data']['workmate'], 'Joe');
		$this->assertInstanceOf('Laravel\\Session\\Payload', $payload->put('something', 'test'));
	}

	public function test_flash_method_puts_item_in_flash_data()
	{
		$payload = new Payload(array());
		$payload->flash('name', 'Taylor');
		$this->assertEquals($payload->session['data'][':new:name'], 'Taylor');
		$this->assertInstanceOf('Laravel\\Session\\Payload', $payload->flash('something', 'test'));
	}

	public function test_reflash_keeps_all_session_data()
	{
		$payload = new Payload(array('data' => array(':old:name' => 'Taylor', ':old:age' => 25)));
		$payload->reflash();
		$this->assertTrue(isset($payload->session['data'][':new:name']));
		$this->assertTrue(isset($payload->session['data'][':new:age']));
		$this->assertFalse(isset($payload->session['data'][':old:name']));
		$this->assertFalse(isset($payload->session['data'][':old:age']));
	}

	public function test_keep_method_keeps_specified_session_data()
	{
		$payload = new Payload(array('data' => array(':old:name' => 'Taylor', ':old:age' => 25)));
		$payload->keep('name');
		$this->assertTrue(isset($payload->session['data'][':new:name']));
		$this->assertFalse(isset($payload->session['data'][':old:name']));
		$payload = new Payload(array('data' => array(':old:name' => 'Taylor', ':old:age' => 25)));
		$payload->keep(array('name', 'age'));
		$this->assertTrue(isset($payload->session['data'][':new:name']));
		$this->assertTrue(isset($payload->session['data'][':new:age']));
		$this->assertFalse(isset($payload->session['data'][':old:name']));
		$this->assertFalse(isset($payload->session['data'][':old:age']));
	}

	public function test_flush_method_clears_payload_data()
	{
		$payload = new Payload(array('data' => array('name' => 'Taylor')));
		$payload->flush();
		$this->assertEquals(count($payload->session['data']), 0);
	}

	public function test_regenerate_session_sets_new_session_id()
	{
		$payload = new Payload(array('id' => 'something'));
		$payload->regenerate();
		$this->assertTrue($payload->regenerated);
		$this->assertEquals(strlen($payload->session['id']), 40);
	}

	public function test_age_method_sets_last_activity_time()
	{
		$data = $this->getDummyData();
		unset($data['last_activity']);
		$payload = new Payload($data);
		$payload->age();
		$this->assertTrue(isset($payload->session['last_activity']));
	}

	public function test_age_method_ages_all_flash_data()
	{
		$payload = new Payload($this->getDummyData());
		$payload->age();
		$this->assertTrue(isset($payload->session['data'][':old:age']));
		$this->assertFalse(isset($payload->session['data'][':old:gender']));
	}

	public function test_age_method_returns_session_array()
	{
		$payload = new Payload($this->getDummyData());
		$age = $payload->age();
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

}