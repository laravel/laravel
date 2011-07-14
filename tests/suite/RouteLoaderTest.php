<?php

class RouteLoaderTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		$this->rrmdir(APP_PATH.'routes');
	}

	public function testRouteArrayShouldBeReturnedWhenUsingSingleRoutesFile()
	{
		$routes = System\Router::load('test');

		$this->assertEquals(count($routes), 1);
		$this->assertArrayHasKey('GET /', $routes);
		$this->assertTrue(is_callable($routes['GET /']));
	}

	public function testRouteLoaderReturnsHomeRoutesWhenItIsOnlyFileInRoutesDirectory()
	{
		mkdir(APP_PATH.'routes', 0777);
		file_put_contents(APP_PATH.'routes/home.php', "<?php return array('GET /' => function() {return '/';}); ?>", LOCK_EX);

		$this->assertEquals(count(System\Router::load('')), 1);
	}

	public function testRouteLoaderWithRoutesDirectory()
	{
		mkdir(APP_PATH.'routes', 0777);
		file_put_contents(APP_PATH.'routes/user.php', "<?php return array('GET /user' => function() {return '/user';}); ?>", LOCK_EX);

		$routes = System\Router::load('user/home');

		$this->assertEquals(count($routes), 2);
		$this->assertArrayHasKey('GET /', $routes);
		$this->assertArrayHasKey('GET /user', $routes);
		$this->assertTrue(is_callable($routes['GET /']));
		$this->assertTrue(is_callable($routes['GET /user']));
	}

	/**
	 * Recursively Remove A Directory.
	 */
	public function rrmdir($dir)
	{
		if (is_dir($dir)) 
		{ 
		 	$objects = scandir($dir); 

		 	foreach ($objects as $object) 
		 	{ 
		   		if ($object != "." && $object != "..") 
		   		{ 
		     		if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object); 
		   		} 
		 	} 

		 	reset($objects); 
		 	rmdir($dir); 
		} 
	}

}