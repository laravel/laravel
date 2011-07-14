<?php

class RouteFinderTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass()
	{
		$routes = array();

		$routes['GET /home'] = array('GET /home' => array('name' => 'home', 'do' => function() {}));
		$routes['GET /user'] = array('GET /user' => array('name' => 'user', 'do' => function() {}));

		System\Route\Finder::$routes = $routes;
	}

	public function testRouteFinderReturnsNullWhenRouteIsNotFound()
	{
		$this->assertNull(System\Route\Finder::find('doesnt-exist'));
	}

	public function testRouteFinderReturnsRouteWhenFoundInSingleRoutesFile()
	{
		$this->assertArrayHasKey('GET /home', System\Route\Finder::find('home'));
		$this->assertArrayHasKey('GET /user', System\Route\Finder::find('user'));
	}

	public function testRouteFinderLoadsRoutesFromRouteDirectoryToFindRoutes()
	{
		System\Route\Finder::$routes = null;
		$this->setupRoutesDirectory();

		$this->assertArrayHasKey('GET /user', System\Route\Finder::find('user'));

		Utils::rrmdir(APP_PATH.'routes');
	}

	private function setupRoutesDirectory()
	{
		mkdir(APP_PATH.'routes', 0777);
		file_put_contents(APP_PATH.'routes/user.php', "<?php return array('GET /user' => array('name' => 'user', 'do' => function() {return '/user';})); ?>", LOCK_EX);		
	}

}