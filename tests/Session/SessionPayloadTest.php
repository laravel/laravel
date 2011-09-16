<?php

use Laravel\Session\Payload;

class SessionPayloadTest extends PHPUnit_Framework_TestCase {

	public function testHasMethodIndicatesIfItemExistsInPayload()
	{
		$payload = new Payload($this->getDummyData());

		$this->assertTrue($payload->has('name'));
		$this->assertTrue($payload->has('age'));
		$this->assertTrue($payload->has('gender'));

		$this->assertFalse($payload->has('something'));
		$this->assertFalse($payload->has('id'));
		$this->assertFalse($payload->has('last_activity'));
	}

	public function testGetMethodReturnsItemFromPayload()
	{
		$payload = new Payload($this->getDummyData());

		$this->assertEquals($payload->get('name'), 'Taylor');
		$this->assertEquals($payload->get('age'), 25);
		$this->assertEquals($payload->get('gender'), 'male');
	}

	public function testGetMethodReturnsDefaultWhenItemDoesntExist()
	{
		$payload = new Payload($this->getDummyData());

		$this->assertNull($payload->get('something'));
		$this->assertEquals('Taylor', $payload->get('something', 'Taylor'));
		$this->assertEquals('Taylor', $payload->get('something', function() {return 'Taylor';}));
	}

	public function testPutMethodAddsToPayload()
	{
		$payload = new Payload($this->getDummyData());

		$payload->put('name', 'Weldon');
		$payload->put('workmate', 'Joe');

		$this->assertEquals($payload->session['data']['name'], 'Weldon');
		$this->assertEquals($payload->session['data']['workmate'], 'Joe');
		$this->assertInstanceOf('Laravel\\Session\\Payload', $payload->put('something', 'test'));
	}

	public function testFlashMethodPutsItemInFlashData()
	{
		$payload = new Payload(array());

		$payload->flash('name', 'Taylor');

		$this->assertEquals($payload->session['data'][':new:name'], 'Taylor');
		$this->assertInstanceOf('Laravel\\Session\\Payload', $payload->flash('something', 'test'));
	}

	public function testReflashKeepsAllSessionData()
	{
		$payload = new Payload(array('data' => array(':old:name' => 'Taylor', ':old:age' => 25)));

		$payload->reflash();

		$this->assertTrue(isset($payload->session['data'][':new:name']));
		$this->assertTrue(isset($payload->session['data'][':new:age']));
		$this->assertFalse(isset($payload->session['data'][':old:name']));
		$this->assertFalse(isset($payload->session['data'][':old:age']));
	}

	public function testKeepMethodKeepsSpecificSessionData()
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

	public function testFlushMethodShouldClearPayloadData()
	{
		$payload = new Payload(array('data' => array('name' => 'Taylor')));

		$payload->flush();

		$this->assertEquals(count($payload->session['data']), 0);
	}

	public function testRegenerateMethodSetsNewSessionID()
	{
		$payload = new Payload(array('id' => 'something'));

		$payload->regenerate();

		$this->assertEquals(strlen($payload->session['id']), 40);
	}

	public function testAgeMethodSetsLastActivityTime()
	{
		$data = $this->getDummyData();

		unset($data['last_activity']);

		$payload = new Payload($data);

		$payload->age();

		$this->assertTrue(isset($payload->session['last_activity']));
	}

	public function testAgeMethodAgesAllFlashData()
	{
		$payload = new Payload($this->getDummyData());

		$payload->age();

		$this->assertTrue(isset($payload->session['data'][':old:age']));
		$this->assertFalse(isset($payload->session['data'][':old:gender']));
	}

	public function testAgeMethodReturnsSessionArray()
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