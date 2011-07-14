<?php

class ViewTest extends PHPUnit_Framework_TestCase {

	public function testConstructorSetsViewNameAndData()
	{
		$view = new System\View('view', array('name' => 'test'));

		$this->assertEquals($view->view, 'view');
		$this->assertEquals($view->data, array('name' => 'test'));

		$view = new System\View('view');
		$this->assertEquals($view->data, array());
	}

	public function testMakeMethodReturnsNewViewInstance()
	{
		$this->assertInstanceOf('System\\View', System\View::make('test'));
	}

	public function testBindMethodAddsItemToViewData()
	{
		$view = System\View::make('test')->bind('name', 'test');
		$this->assertEquals($view->data, array('name' => 'test'));
	}

	public function testBoundViewDataCanBeRetrievedThroughMagicMethods()
	{
		$view = System\View::make('test')->bind('name', 'test');

		$this->assertTrue(isset($view->name));
		$this->assertEquals($view->name, 'test');

		unset($view->name);
		$this->assertFalse(isset($view->name));
	}

	public function testGetMethodReturnsStringContentOfView()
	{
		$this->assertTrue(is_string(System\View::make('home/index')->get()));
	}

}