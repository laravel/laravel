<?php

define('TEST_BASE_PATH', dirname(realpath(__FILE__)) . '/');

class RoutesTest extends PHPUnit_Framework_TestCase {
	private $route;
	private $request;
	
	public static function setUpBeforeClass()
	{
		IoC::container()->register('laravel.routing.router', function($c)
		{
			return new \Laravel\Routing\Router($c->resolve('laravel.routing.loader'), TEST_BASE_PATH.'controllers/');
		}, true);

		// changing paths
		IoC::container()->register('laravel.routing.caller', function($c)
		{
			return new \Laravel\Routing\Caller($c, require TEST_BASE_PATH.'filters'.EXT, TEST_BASE_PATH.'controllers/');
		});
		
		IoC::container()->register('laravel.routing.loader', function($c)
		{
			return new \Laravel\Routing\Loader(TEST_BASE_PATH,TEST_BASE_PATH.'routes/');
		}, true);
	}
	
	protected function setUp()
	{
		$this->route = null;
		$this->request = null;
		
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
	 * tests bad routes
	 */
	public function testBad()
	{
		$response = $this->processRoute('/test/bad');
		$this->assertEquals($response->content->view, 'error/404');
	}

	/**
	 * tests bad routes exceptions
	 */
	public function testBadException() {
		$this->setExpectedException('Exception');
		$response = $this->processRoute('/test/bad2');
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
	 * tests nested routes (in the routes folder)
	 */
	public function testNested()
	{		
		$response = $this->processRoute('/nested/test');	
		$this->assertEquals($response->content, 'nested test');
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
	
	/**
	 * named routes
	 */
	public function testNamed()
	{
		$response = $this->processRoute('/test/named');
		$this->assertEquals($this->route, $this->request->route());
		
		$this->assertTrue($this->route->is_named_route());
		$this->assertTrue($this->route->handles('test/named'));
		$this->assertFalse($this->route->handles('test/direct'));
		$this->assertEquals($this->route->not_exist(), null);
	}
	
	/**
	 * creating urls for named routes
	 */
	public function testNamedURL()
	{
		$url = URL::to_named_route_params(array('var'));
		$this->assertStringEndsWith('/test/named/var', $url);
		
		$url = URL::to_secure_named_route_params(array('var'));
		$this->assertStringStartsWith('https', $url);
	}

	/**
	 * tests url exception
	 */
	public function testURLException() {
		$this->setExpectedException('Exception', 'Error generating named route for route [not_exist]. Route is not defined.');
		URL::to_not_exist();
	}
	
	/**
	 * redirect to named route
	 */
	public function testNamedRedirect()
	{
		$redirect = Redirect::to_named_route();
		$this->assertObjectHasAttribute('headers', $redirect);
		/* these don't work since Response::headers is protected
		$this->assertArrayHasKey('Location', $redirect->headers);
		$this->assertStringEndsWith('/test/named', $redirect->headers['Location']);
		*/
	
		$this->markTestIncomplete('Response::headers is protected so can\'t check to see if redirect worked correctly');
	}

	/**
	 * tests controllers
	 */
	public function testControllers() {
		$response = $this->processRoute('/controller');
		$this->assertEquals($response->content, 'controller/index');

		$response = $this->processRoute('/controller/action');
		$this->assertEquals($response->content, 'controller/action');

		$response = $this->processRoute('/controller/action/param');
		$this->assertEquals($response->content, 'controller/action/param');

		$response = $this->processRoute('/controller/action/param/param2');
		$this->assertEquals($response->content, 'controller/action/param/param2');

		$response = $this->processRoute('/controller/view');
		$this->assertTrue($response->content instanceof \Laravel\View);
		$this->assertEquals($response->content->view, 'home.index');

		$response = $this->processRoute('/controller/custom');
		$this->assertEquals($response->content, 'custom');

		$response = $this->processRoute('/controller/notexists');
		$this->assertEquals($response->content->view, 'error/404');

		$response = $this->processRoute('/resolve');
		$this->assertEquals($response->content, 'resolve/index/variable');

		$response = $this->processRoute('/bad');
		$this->assertEquals($response->content->view, 'error/404');
	}

	/**
	 * tests controllers exception
	 */
	public function testControllersException() {
		$this->setExpectedException('Exception', 'Attempting to access undefined property [notvar] on controller.');
		$response = $this->processRoute('/controller/notvar');
	}

	private function processRoute($uri, $method = 'GET')
	{
		$_SERVER['REQUEST_URI'] = $uri;
		$_SERVER['REQUEST_METHOD'] = $method;
	
		// not using container resolve because it is a singleton and that makes it so we can't change $_SERVER
		$this->request = new \Laravel\Request(new \Laravel\URI($_SERVER), $_SERVER, $_POST);
		$router = IoC::container()->resolve('laravel.routing.router');
	
		list($method, $uri) = array($this->request->method(), $this->request->uri());
		$this->route = $router->route($this->request, $method, $uri);

		if ( ! is_null($this->route))
		{
			$response = IoC::container()->resolve('laravel.routing.caller')->call($this->route);
		}
		else
		{
			$response = Response::error('404');
		}
		
		return $response;
	}
}