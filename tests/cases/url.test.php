<?php

use Laravel\Routing\Router;

class URLTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test enviornment.
	 */
	public function setUp()
	{
		Router::$routes = array();
		Router::$names = array();
		Router::$uses = array();
		Router::$fallback = array();
		Config::set('application.url', 'http://localhost');
	}

	/**
	 * Destroy the test enviornment.
	 */
	public function tearDown()
	{
		$_SERVER = array();
		Router::$routes = array();
		Router::$names = array();
		Router::$uses = array();
		Router::$fallback = array();
		Config::set('application.ssl', true);
		Config::set('application.url', '');
		Config::set('application.index', 'index.php');
	}

	/**
	 * Test the URL::to method.
	 *
	 * @group laravel
	 */
	public function testToMethodGeneratesURL()
	{
		$this->assertEquals('http://localhost/index.php/user/profile', URL::to('user/profile'));
		$this->assertEquals('https://localhost/index.php/user/profile', URL::to('user/profile', true));

		Config::set('application.index', '');

		$this->assertEquals('http://localhost/user/profile', URL::to('user/profile'));
		$this->assertEquals('https://localhost/user/profile', URL::to('user/profile', true));

		Config::set('application.ssl', false);

		$this->assertEquals('http://localhost/user/profile', URL::to('user/profile', true));
	}

	/**
	 * Test the URL::to_action method.
	 *
	 * @group laravel
	 */
	public function testToActionMethodGeneratesURLToControllerAction()
	{
		Route::get('foo/bar/(:any?)', 'foo@baz');
		$this->assertEquals('http://localhost/index.php/x/y', URL::to_action('x@y'));
		$this->assertEquals('http://localhost/index.php/x/y/Taylor', URL::to_action('x@y', array('Taylor')));
		$this->assertEquals('http://localhost/index.php/foo/bar', URL::to_action('foo@baz'));
		$this->assertEquals('http://localhost/index.php/foo/bar/Taylor', URL::to_action('foo@baz', array('Taylor')));
	}

	/**
	 * Test the URL::to_asset method.
	 *
	 * @group laravel
	 */
	public function testToAssetGeneratesURLWithoutFrontControllerInURL()
	{
		$this->assertEquals('http://localhost/image.jpg', URL::to_asset('image.jpg'));
		$this->assertEquals('https://localhost/image.jpg', URL::to_asset('image.jpg', true));

		Config::set('application.index', '');

		$this->assertEquals('http://localhost/image.jpg', URL::to_asset('image.jpg'));
		$this->assertEquals('https://localhost/image.jpg', URL::to_asset('image.jpg', true));

		$_SERVER['HTTPS'] = 'on';

		$this->assertEquals('https://localhost/image.jpg', URL::to_asset('image.jpg'));
	}

	/**
	 * Test the URL::to_route method.
	 *
	 * @group laravel
	 */
	public function testToRouteMethodGeneratesURLsToRoutes()
	{
		Route::get('url/test', array('as' => 'url-test'));
		Route::get('url/test/(:any)/(:any?)', array('as' => 'url-test-2'));
		Route::get('url/secure/(:any)/(:any?)', array('as' => 'url-test-3', 'https' => true));

		$this->assertEquals('http://localhost/index.php/url/test', URL::to_route('url-test'));
		$this->assertEquals('http://localhost/index.php/url/test/taylor', URL::to_route('url-test-2', array('taylor')));
		$this->assertEquals('https://localhost/index.php/url/secure/taylor', URL::to_route('url-test-3', array('taylor')));
		$this->assertEquals('http://localhost/index.php/url/test/taylor/otwell', URL::to_route('url-test-2', array('taylor', 'otwell')));
	}

}