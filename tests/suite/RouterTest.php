<?php

class RoutingTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass()
	{
		$routes = array();

		$routes['GET /'] = array('name' => 'root', 'do' => function() {});
		$routes['GET /home'] = array('name' => 'home', 'do' => function() {});
		$routes['POST /home'] = array('name' => 'post-home', 'do' => function() {});
		$routes['GET /user/(:num)'] = array('name' => 'user', 'do' => function() {});
		$routes['GET /user/(:any)/(:num)/edit'] = array('name' => 'edit', 'do' => function() {});
		$routes['GET /cart/(:num?)'] = array('name' => 'cart', 'do' => function() {});
		$routes['GET /download/(:num?)/(:any?)'] = array('name' => 'download', 'do' => function() {});

		System\Router::$routes = $routes;
	}

	public static function tearDownAfterClass()
	{
		System\Router::$routes = null;
	}

	public function tearDown()
	{
		Utils::rrmdir(APP_PATH.'routes');
	}

	public function testRouterReturnsNullWhenNotFound()
	{
		$this->assertNull(System\Router::route('GET', 'doesnt-exist'));
	}

	public function testRouterRoutesToRootWhenItIsRequest()
	{
		$this->assertEquals(System\Router::route('GET', '/')->callback['name'], 'root');
	}

	public function testRouterRoutesToProperRouteWhenSegmentsArePresent()
	{
		$this->assertEquals(System\Router::route('GET', 'home')->callback['name'], 'home');
		$this->assertEquals(System\Router::route('GET', 'user/1')->callback['name'], 'user');
		$this->assertEquals(System\Router::route('GET', 'user/taylor/25/edit')->callback['name'], 'edit');
		$this->assertEquals(System\Router::route('POST', 'home')->callback['name'], 'post-home');
	}

	public function testRouterRoutesToProperRouteWhenUsingOptionalSegments()
	{
		$this->assertEquals(System\Router::route('GET', 'cart')->callback['name'], 'cart');
		$this->assertEquals(System\Router::route('GET', 'cart/1')->callback['name'], 'cart');
		$this->assertEquals(System\Router::route('GET', 'download')->callback['name'], 'download');
		$this->assertEquals(System\Router::route('GET', 'download/1')->callback['name'], 'download');
		$this->assertEquals(System\Router::route('GET', 'download/1/a')->callback['name'], 'download');
	}

	public function testRouterReturnsNullWhenRouteNotFound()
	{
		$this->assertNull(System\Router::route('GET', 'user/taylor/taylor/edit'));
		$this->assertNull(System\Router::route('GET', 'user/taylor'));
		$this->assertNull(System\Router::route('GET', 'user/12-3'));
		$this->assertNull(System\Router::route('GET', 'cart/a'));
		$this->assertNull(System\Router::route('GET', 'cart/12-3'));
		$this->assertNull(System\Router::route('GET', 'download/a'));
		$this->assertNull(System\Router::route('GET', 'download/1a'));
		$this->assertNull(System\Router::route('POST', 'user/taylor/25/edit'));
	}

	public function testRouteArrayShouldBeReturnedWhenUsingSingleRoutesFile()
	{
		$routes = System\Router::load('test');

		// Only the Laravel default route should be returned.
		$this->assertArrayHasKey('GET /', $routes);
	}

	public function testRouteLoaderLoadsRouteFilesInRouteDirectoryByURI()
	{
		$this->setupRoutesDirectory();

		$this->assertArrayHasKey('GET /user', System\Router::load('user'));
		$this->assertArrayHasKey('GET /cart/edit', System\Router::load('cart'));
		$this->assertArrayHasKey('GET /cart/edit', System\Router::load('cart/edit'));
	}

	public function testRouteLoaderLoadsBaseRoutesFileForEveryRequest()
	{
		$this->setupRoutesDirectory();
		$this->assertArrayHasKey('GET /', System\Router::load('user'));
	}

	private function setupRoutesDirectory()
	{
		mkdir(APP_PATH.'routes', 0777);

		file_put_contents(APP_PATH.'routes/user.php', "<?php return array('GET /user' => function() {return '/user';}); ?>", LOCK_EX);		
		file_put_contents(APP_PATH.'routes/cart.php', "<?php return array('GET /cart/edit' => function() {return '/cart/edit';}); ?>", LOCK_EX);		
	}

	public function testParameterMethodReturnsNoParametersWhenNoneArePresent()
	{
		$this->assertEmpty(System\Router::parameters('GET /test/route', 'GET /test/route'));
		$this->assertEmpty(System\Router::parameters('GET /', 'GET /'));
	}

	public function testParameterMethodReturnsParametersWhenTheyArePresent()
	{
		$this->assertEquals(System\Router::parameters('GET /user/1', 'GET /user/(:num)'), array(1));
		$this->assertEquals(System\Router::parameters('GET /user/1/2', 'GET /user/(:num)/(:num)'), array(1, 2));
		$this->assertEquals(System\Router::parameters('GET /user/1/test', 'GET /user/(:num)/(:any)'), array(1, 'test'));
		$this->assertEquals(System\Router::parameters('GET /user/1/test/again', 'GET /user/(:num)/test/(:any)'), array(1, 'again'));
	}

}