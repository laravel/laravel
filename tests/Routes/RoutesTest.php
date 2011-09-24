<?php

define('TEST_BASE_PATH', dirname(realpath(__FILE__)) . '/');

class RoutesTest extends PHPUnit_Framework_TestCase {
	public static function setUpBeforeClass() {
		// changing paths
		IoC::container()->register('laravel.routing.caller', function($c)
		{
			return new \Laravel\Routing\Caller($c, require TEST_BASE_PATH.'filters'.EXT, CONTROLLER_PATH);
		});
		
		IoC::container()->register('laravel.routing.loader', function($c)
		{
			return new \Laravel\Routing\Loader(TEST_BASE_PATH,TEST_BASE_PATH . 'routes/');
		}, true);
	}
	
	protected function setUp()
	{
		$_POST = array();
		
		unset($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
	}
	
	/**
	 * tests
	 * GET /test/wildcard/(:num)/(:any)
	 * GET /test/optwildcard/(:any+)
	 */
	public function testWildCards()
	{		
		$response = $this->processRoute('/test/wildcard/123456/joe');
		$this->assertEquals($response->content, '123456/joe');
		
		$response = $this->processRoute('/test/wildcard/123456');
		$this->assertEquals($response->content->view, 'error/404');
		
		$response = $this->processRoute('/test/wildcard/abc123');
		$this->assertEquals($response->content, 'abc123');
		
		$response = $this->processRoute('/test/optwildcard/foo');
		$this->assertEquals($response->content, 'foo');
		
		$response = $this->processRoute('/test/optwildcard');
		$this->assertEquals($response->content, '');
	}
	
	/**
	 * tests GET /test/direct
	 */
	public function testDirect()
	{		
		$response = $this->processRoute('/test/direct');	
		$this->assertEquals($response->content, 'direct');
		
		$response = $this->processRoute('/test/doesnt/exist');
		$this->assertEquals($response->content->view, 'error/404');
	}
	
	/**
	 * tests GET /test/multi and GET /test/altmulti
	 * both routes are the same
	 */
	public function testMultiRoutes()
	{
		$response = $this->processRoute('/test/multi');	
		$this->assertEquals($response->content, 'multi test');
		
		$response = $this->processRoute('/test/altmulti');	
		$this->assertEquals($response->content, 'multi test');
	}
	
	/**
	 * tests post
	 */
	public function testPost()
	{
		$response = $this->processRoute('/test/postrequest', 'POST');	
		$this->assertEquals($response->content, 'POST request');
	}
	
	/**
	 * tests route spoofing
	 */
	public function testSpoofing()
	{
		$_POST['__spoofer'] = 'PUT';		
		$response = $this->processRoute('/test/putrequest');	
		$this->assertEquals($response->content, 'PUT request');
	}
	
	/**
	 * tests filters
	 */
	public function testFilters()
	{
		$response = $this->processRoute('/test/filter/before');
		$this->assertEquals($response->content, 'filtered before');
		
		$response = $this->processRoute('/test/filter/after');
		$this->assertEquals($response->content, 'filtered after');
		
		$response = $this->processRoute('/test/filter/multi');
		$this->assertEquals($response->content, 'filtered after filtered after2');
	}
	
	private function processRoute($uri, $method = 'GET')
	{
		$_SERVER['REQUEST_URI'] = $uri;
		$_SERVER['REQUEST_METHOD'] = $method;
	
		// not using container resolve because it is a singleton and that makes it so we can't change $_SERVER
		$request = new \Laravel\Request(new \Laravel\URI($_SERVER), $_SERVER, $_POST);
		$router = IoC::container()->resolve('laravel.routing.router');
	
		list($method, $uri) = array($request->method(), $request->uri());
		$route = $router->route($request, $method, $uri);

		if ( ! is_null($route))
		{
			$response = IoC::container()->resolve('laravel.routing.caller')->call($route);
		}
		else
		{
			$response = Response::error('404');
		}
		
		return $response;
	}
}