<?php use Laravel\URL, Laravel\Config;

class UrlTest extends PHPUnit_Framework_TestCase {

	public function test_simple_url()
	{
		$this->assertEquals(URL::to(''), 'http://localhost/index.php/');
		$this->assertEquals(URL::to('something'), 'http://localhost/index.php/something');
	}

	public function test_simple_url_without_index()
	{
		Config::set('application.index', '');

		$this->assertEquals(Url::to(''), 'http://localhost/');
		$this->assertEquals(Url::to('something'), 'http://localhost/something');

		Config::set('application.index', 'index.php');
	}

	public function test_asset_url()
	{
		$this->assertEquals(URL::to_asset('img/test.jpg'), 'http://localhost/img/test.jpg');

		Config::set('application.index', '');

		$this->assertEquals(URL::to_asset('img/test.jpg'), 'http://localhost/img/test.jpg');

		Config::set('application.index', 'index.php');
	}

	public function test_secure_url()
	{
		$this->assertEquals(URL::to_secure('something'), 'https://localhost/index.php/something');

		Config::set('application.ssl', false);

		$this->assertEquals(URL::to_secure('something'), 'http://localhost/index.php/something');

		Config::set('application.ssl', true);
	}

	public function test_slug()
	{
		$this->assertEquals(URL::slug('My favorite blog!!'), 'my-favorite-blog');
		$this->assertEquals(URL::slug('My favorite blog!!', '_'), 'my_favorite_blog');
	}

}