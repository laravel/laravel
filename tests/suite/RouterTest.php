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

	public function testRouterGivesRouteProperSegmentsWhenTheyArePresent()
	{
		$this->assertEquals(System\Router::route('GET', 'user/1')->parameters[0], 1);
		$this->assertEquals(count(System\Router::route('GET', 'user/1')->parameters), 1);

		$this->assertEquals(System\Router::route('GET', 'user/taylor/25/edit')->parameters[0], 'taylor');
		$this->assertEquals(System\Router::route('GET', 'user/taylor/25/edit')->parameters[1], 25);
		$this->assertEquals(count(System\Router::route('GET', 'user/taylor/25/edit')->parameters), 2);		
	}

	public function testRouterRoutesToProperRouteWhenUsingOptionalSegments()
	{
		$this->assertEquals(System\Router::route('GET', 'cart')->callback['name'], 'cart');
		$this->assertEquals(System\Router::route('GET', 'cart/1')->callback['name'], 'cart');
		$this->assertEquals(System\Router::route('GET', 'download')->callback['name'], 'download');
		$this->assertEquals(System\Router::route('GET', 'download/1')->callback['name'], 'download');
		$this->assertEquals(System\Router::route('GET', 'download/1/a')->callback['name'], 'download');
	}

	public function testRouterGivesRouteProperOptionalSegmentsWhenTheyArePresent()
	{
		$this->assertTrue(is_array(System\Router::route('GET', 'cart')->parameters));
		$this->assertEquals(count(System\Router::route('GET', 'cart')->parameters), 0);
		$this->assertEquals(System\Router::route('GET', 'cart/1')->parameters[0], 1);

		$this->assertEquals(count(System\Router::route('GET', 'download')->parameters), 0);
		$this->assertEquals(System\Router::route('GET', 'download/1')->parameters[0], 1);
		$this->assertEquals(count(System\Router::route('GET', 'download/1')->parameters), 1);

		$this->assertEquals(System\Router::route('GET', 'download/1/a')->parameters[0], 1);
		$this->assertEquals(System\Router::route('GET', 'download/1/a')->parameters[1], 'a');
		$this->assertEquals(count(System\Router::route('GET', 'download/1/a')->parameters), 2);
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

	public function testRouteLoaderShouldReturnSingleRoutesFileWhenNoFolderIsPresent()
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

}