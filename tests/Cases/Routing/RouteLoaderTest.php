<?php use Laravel\Routing\Loader;

class RouteLoaderTest extends PHPUnit_Framework_TestCase {

	public function test_loader_can_load_base_routes()
	{
		$loader = $this->getLoader();

		$routes = $loader->load('/');

		$this->assertEquals(count($routes), 2);
		$this->assertTrue(array_key_exists('GET /', $routes));
		$this->assertTrue(array_key_exists('GET /root', $routes));
	}

	public function test_loader_can_load_single_nested_routes()
	{
		$loader = $this->getLoader();

		$routes = $loader->load('user');

		$this->assertEquals(count($routes), 4);
		$this->assertTrue(array_key_exists('GET /user', $routes));
		$this->assertTrue(array_key_exists('GET /user/profile', $routes));
	}

	public function test_loader_can_load_multi_nested_routes()
	{
		$loader = $this->getLoader();

		$routes = $loader->load('admin/panel');

		$this->assertEquals(count($routes), 4);
		$this->assertTrue(array_key_exists('GET /admin/panel/show', $routes));
		$this->assertTrue(array_key_exists('GET /admin/panel/update', $routes));
	}

	public function test_everything_loads_all_routes()
	{
		$loader = $this->getLoader();

		$routes = $loader->everything();

		$this->assertEquals(count($routes), 6);
		
	}

	private function getLoader()
	{
		return new Loader(FIXTURE_PATH.'RouteLoader/', FIXTURE_PATH.'RouteLoader/routes/');
	}

}
