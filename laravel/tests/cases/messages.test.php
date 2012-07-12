<?php

class MessagesTest extends PHPUnit_Framework_TestCase {

	/**
	 * The Messages instance.
	 *
	 * @var Messages
	 */
	public $messages;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$this->messages = new Laravel\Messages;
	}

	/**
	 * Test the Messages::add method.
	 *
	 * @group laravel
	 */
	public function testAddingMessagesDoesNotCreateDuplicateMessages()
	{
		$this->messages->add('email', 'test');
		$this->messages->add('email', 'test');
		$this->assertCount(1, $this->messages->messages);
	}

	/**
	 * Test the Messages::add method.
	 *
	 * @group laravel
	 */
	public function testAddMethodPutsMessageInMessagesArray()
	{
		$this->messages->add('email', 'test');
		$this->assertArrayHasKey('email', $this->messages->messages);
		$this->assertEquals('test', $this->messages->messages['email'][0]);
	}

	/**
	 * Test the Messages::has method.
	 *
	 * @group laravel
	 */
	public function testHasMethodReturnsTrue()
	{
		$this->messages->add('email', 'test');
		$this->assertTrue($this->messages->has('email'));
	}

	/**
	 * Test the Messages::has method.
	 *
	 * @group laravel
	 */
	public function testHasMethodReturnsFalse()
	{
		$this->assertFalse($this->messages->has('something'));
	}

	/**
	 * Test the Messages::first method.
	 *
	 * @group laravel
	 */
	public function testFirstMethodReturnsSingleString()
	{
		$this->messages->add('email', 'test');
		$this->assertEquals('test', $this->messages->first('email'));
		$this->assertEquals('', $this->messages->first('something'));
	}

	/**
	 * Test the Messages::get method.
	 *
	 * @group laravel
	 */
	public function testGetMethodReturnsAllMessagesForAttribute()
	{
		$messages = array('email' => array('something', 'else'));
		$this->messages->messages = $messages;
		$this->assertEquals(array('something', 'else'), $this->messages->get('email'));
	}

	/**
	 * Test the Messages::all method.
	 *
	 * @group laravel
	 */
	public function testAllMethodReturnsAllErrorMessages()
	{
		$messages = array('email' => array('something', 'else'), 'name' => array('foo'));
		$this->messages->messages = $messages;
		$this->assertEquals(array('something', 'else', 'foo'), $this->messages->all());
	}

	/**
	 * Test the Messages::get method.
	 *
	 * @group laravel
	 */
	public function testMessagesRespectFormat()
	{
		$this->messages->add('email', 'test');
		$this->assertEquals('<p>test</p>', $this->messages->first('email', '<p>:message</p>'));
		$this->assertEquals(array('<p>test</p>'), $this->messages->get('email', '<p>:message</p>'));
		$this->assertEquals(array('<p>test</p>'), $this->messages->all('<p>:message</p>'));
	}


}