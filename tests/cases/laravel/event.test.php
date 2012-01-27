<?php

class EventTest extends PHPUnit_Framework_TestCase {

	/**
	 * Tear down the testing environment.
	 */
	public function tearDown()
	{
		Event::$events = array();
	}

	/**
	 * Test basic event firing.
	 *
	 * @group laravel
	 */
	public function testListenersAreFiredForEvents()
	{
		Event::listen('test.event', function()
		{
			return 1;
		});

		Event::listen('test.event', function()
		{
			return 2;
		});

		$responses = Event::fire('test.event');

		$this->assertEquals(1, $responses[0]);
		$this->assertEquals(2, $responses[1]);
	}

	/**
	 * Test parameters can be passed to event listeners.
	 *
	 * @group laravel
	 */
	public function testParametersCanBePassedToEvents()
	{
		Event::listen('test.event', function($var) { return $var; });

		$responses = Event::fire('test.event', array('Taylor'));

		$this->assertEquals('Taylor', $responses[0]);
	}

}