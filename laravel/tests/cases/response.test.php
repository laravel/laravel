<?php

class ResponseTest extends PHPUnit_Framework_TestCase {

	/**
	 * Test the Response::make method.
	 *
	 * @group laravel
	 */
	public function testMakeMethodProperlySetsContent()
	{
		$response = Response::make('foo', 201, array('bar' => 'baz'));

		$this->assertEquals('foo', $response->content);
		$this->assertEquals(201, $response->status());
		$this->assertArrayHasKey('bar', $response->headers()->all());
		$this->assertEquals('baz', $response->headers()->get('bar'));
	}

	/**
	 * Test the Response::view method.
	 *
	 * @group laravel
	 */
	public function testViewMethodSetsContentToView()
	{
		$response = Response::view('home.index', array('name' => 'Taylor'));

		$this->assertEquals('home.index', $response->content->view);
		$this->assertEquals('Taylor', $response->content->data['name']);
	}

	/**
	 * Test the Response::error method.
	 *
	 * @group laravel
	 */
	public function testErrorMethodSetsContentToErrorView()
	{
		$response = Response::error('404', array('name' => 'Taylor'));

		$this->assertEquals(404, $response->status());
		$this->assertEquals('error.404', $response->content->view);
		$this->assertEquals('Taylor', $response->content->data['name']);
	}

	/**
	 * Test the Response::prepare method.
	 *
	 * @group laravel
	 */
	public function testPrepareMethodCreatesAResponseInstanceFromGivenValue()
	{
		$response = Response::prepare('Taylor');

		$this->assertInstanceOf('Laravel\\Response', $response);
		$this->assertEquals('Taylor', $response->content);

		$response = Response::prepare(new Response('Taylor'));

		$this->assertInstanceOf('Laravel\\Response', $response);
		$this->assertEquals('Taylor', $response->content);
	}

	/**
	 * Test the Response::header method.
	 *
	 * @group laravel
	 */
	public function testHeaderMethodSetsValueInHeaderArray()
	{
		$response = Response::make('')->header('foo', 'bar');

		$this->assertEquals('bar', $response->headers()->get('foo'));
	}

	/**
	 * Test the Response::status method.
	 *
	 * @group laravel
	 */
	public function testStatusMethodSetsStatusCode()
	{
		$response = Response::make('')->status(404);

		$this->assertEquals(404, $response->status());
	}

}