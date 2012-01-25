<?php

class ViewTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test the View class constructor.
	 *
	 * @group laravel
	 */
	public function testViewNameIsSetByConstrutor()
	{
		$view = new View('home.index');

		$this->assertEquals('home.index', $view->view);
	}

	/**
	 * Test the View class constructor.
	 *
	 * @group laravel
	 */
	public function testViewIsCreatedWithCorrectPath()
	{
		$view = new View('home.index');

		$this->assertEquals(APP_PATH.'views/home/index.php', $view->path);
	}

	/**
	 * Test the View class constructor.
	 *
	 * @group laravel
	 */
	public function testDataIsSetOnViewByConstructor()
	{
		$view = new View('home.index', array('name' => 'Taylor'));

		$this->assertEquals('Taylor', $view->data['name']);
	}

	/**
	 * Test the View class constructor.
	 *
	 * @group laravel
	 */
	public function testEmptyMessageContainerSetOnViewWhenNoErrorsInSession()
	{
		$view = new View('home.index');

		$this->assertInstanceOf('Laravel\\Messages', $view->data['errors']);
	}

	/**
	 * Test the View __set method.
	 *
	 * @group laravel
	 */
	public function testDataCanBeSetOnViewsThroughMagicMethods()
	{
		$view = new View('home.index');

		$view->comment = 'Taylor';

		$this->assertEquals('Taylor', $view->data['comment']);
	}

	/**
	 * Test the View __get method.
	 *
	 * @group laravel
	 */
	public function testDataCanBeRetrievedFromViewsThroughMagicMethods()
	{
		$view = new View('home.index');

		$view->comment = 'Taylor';

		$this->assertEquals('Taylor', $view->comment);
	}

	/**
	 * Test the View's ArrayAccess implementation.
	 *
	 * @group laravel
	 */
	public function testDataCanBeSetOnTheViewThroughArrayAccess()
	{
		$view = new View('home.index');

		$view['comment'] = 'Taylor';

		$this->assertEquals('Taylor', $view->data['comment']);
	}

	/**
	 * Test the View's ArrayAccess implementation.
	 *
	 * @group laravel
	 */
	public function testDataCanBeRetrievedThroughArrayAccess()
	{
		$view = new View('home.index');

		$view['comment'] = 'Taylor';

		$this->assertEquals('Taylor', $view['comment']);
	}

}