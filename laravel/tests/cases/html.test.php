<?php

class HtmlTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Setup the test environment
	 */
	public function setUp()
	{
		URL::$base = null;
		Config::set('application.url', 'http://localhost');
		Config::set('application.index', 'index.php');
		Router::$names = array();
		Router::$routes = array();
	}

	/**
	 * Destroy the test environment
	 */
	public function tearDown()
	{
		Config::set('application.url', '');
		Config::set('application.index', 'index.php');
		Router::$names = array();
		Router::$routes = array();
	}

	/**
	 * Test generating a link to JavaScript files
	 *
	 * @group laravel
	 */
	public function testGeneratingScript()
	{
		$html1 = HTML::script('foo.js');
		$html2 = HTML::script('http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js');
		$html3 = HTML::script('foo.js', array('type' => 'text/javascript'));

		$this->assertEquals('<script src="http://localhost/foo.js"></script>'.PHP_EOL, $html1);
		$this->assertEquals('<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>'.PHP_EOL, $html2);
		$this->assertEquals('<script src="http://localhost/foo.js" type="text/javascript"></script>'.PHP_EOL, $html3);
	}

	/**
	 * Test generating a link to CSS files
	 *
	 * @group laravel
	 */
	public function testGeneratingStyle()
	{
		$html1 = HTML::style('foo.css');
		$html2 = HTML::style('http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/js/bootstrap.min.js');
		$html3 = HTML::style('foo.css', array('media' => 'print'));

		$this->assertEquals('<link href="http://localhost/foo.css" media="all" type="text/css" rel="stylesheet">'.PHP_EOL, $html1);
		$this->assertEquals('<link href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/js/bootstrap.min.js" media="all" type="text/css" rel="stylesheet">'.PHP_EOL, $html2);
		$this->assertEquals('<link href="http://localhost/foo.css" media="print" type="text/css" rel="stylesheet">'.PHP_EOL, $html3);
	}

	/**
	 * Test generating proper span
	 *
	 * @group laravel
	 */
	public function testGeneratingSpan()
	{
		$html1 = HTML::span('foo');
		$html2 = HTML::span('foo', array('class' => 'badge'));

		$this->assertEquals('<span>foo</span>', $html1);
		$this->assertEquals('<span class="badge">foo</span>', $html2);
	}

	/**
	 * Test generating proper link
	 *
	 * @group laravel
	 */
	public function testGeneratingLink()
	{
		$html1 = HTML::link('foo');
		$html2 = HTML::link('foo', 'Foobar');
		$html3 = HTML::link('foo', 'Foobar', array('class' => 'btn'));
		$html4 = HTML::link('http://google.com', 'Google');

		$this->assertEquals('<a href="http://localhost/index.php/foo">http://localhost/index.php/foo</a>', $html1);
		$this->assertEquals('<a href="http://localhost/index.php/foo">Foobar</a>', $html2);
		$this->assertEquals('<a href="http://localhost/index.php/foo" class="btn">Foobar</a>', $html3);
		$this->assertEquals('<a href="http://google.com">Google</a>', $html4);
	}

	/**
	 * Test generating proper link to secure
	 *
	 * @group laravel
	 */
	public function testGeneratingLinkToSecure()
	{
		$html1 = HTML::link_to_secure('foo');
		$html2 = HTML::link_to_secure('foo', 'Foobar');
		$html3 = HTML::link_to_secure('foo', 'Foobar', array('class' => 'btn'));
		$html4 = HTML::link_to_secure('http://google.com', 'Google');

		$this->assertEquals('<a href="https://localhost/index.php/foo">https://localhost/index.php/foo</a>', $html1);
		$this->assertEquals('<a href="https://localhost/index.php/foo">Foobar</a>', $html2);
		$this->assertEquals('<a href="https://localhost/index.php/foo" class="btn">Foobar</a>', $html3);
		$this->assertEquals('<a href="http://google.com">Google</a>', $html4);
	}

	/**
	 * Test generating proper link to asset
	 *
	 * @group laravel
	 */
	public function testGeneratingAssetLink()
	{
		$html1 = HTML::link_to_asset('foo.css');
		$html2 = HTML::link_to_asset('foo.css', 'Foobar');
		$html3 = HTML::link_to_asset('foo.css', 'Foobar', array('class' => 'btn'));
		$html4 = HTML::link_to_asset('http://google.com/images.jpg', 'Google');

		$this->assertEquals('<a href="http://localhost/foo.css">http://localhost/foo.css</a>', $html1);
		$this->assertEquals('<a href="http://localhost/foo.css">Foobar</a>', $html2);
		$this->assertEquals('<a href="http://localhost/foo.css" class="btn">Foobar</a>', $html3);
		$this->assertEquals('<a href="http://google.com/images.jpg">Google</a>', $html4);
	}

	/**
	 * Test generating proper link to secure asset
	 *
	 * @group laravel
	 */
	public function testGeneratingAssetLinkToSecure()
	{
		$html1 = HTML::link_to_secure_asset('foo.css');
		$html2 = HTML::link_to_secure_asset('foo.css', 'Foobar');
		$html3 = HTML::link_to_secure_asset('foo.css', 'Foobar', array('class' => 'btn'));
		$html4 = HTML::link_to_secure_asset('http://google.com/images.jpg', 'Google');

		$this->assertEquals('<a href="https://localhost/foo.css">https://localhost/foo.css</a>', $html1);
		$this->assertEquals('<a href="https://localhost/foo.css">Foobar</a>', $html2);
		$this->assertEquals('<a href="https://localhost/foo.css" class="btn">Foobar</a>', $html3);
		$this->assertEquals('<a href="http://google.com/images.jpg">Google</a>', $html4);
	}

	/**
	 * Test generating proper link to route
	 *
	 * @group laravel
	 */
	public function testGeneratingLinkToRoute()
	{
		Route::get('dashboard', array('as' => 'foo'));

		$html1 = HTML::link_to_route('foo');
		$html2 = HTML::link_to_route('foo', 'Foobar');
		$html3 = HTML::link_to_route('foo', 'Foobar', array(), array('class' => 'btn'));

		$this->assertEquals('<a href="http://localhost/index.php/dashboard">http://localhost/index.php/dashboard</a>', $html1);
		$this->assertEquals('<a href="http://localhost/index.php/dashboard">Foobar</a>', $html2);
		$this->assertEquals('<a href="http://localhost/index.php/dashboard" class="btn">Foobar</a>', $html3);	
	}

	/**
	 * Test generating proper link to action
	 *
	 * @group laravel
	 */
	public function testGeneratingLinkToAction()
	{
		$html1 = HTML::link_to_action('foo@bar');
		$html2 = HTML::link_to_action('foo@bar', 'Foobar');
		$html3 = HTML::link_to_action('foo@bar', 'Foobar', array(), array('class' => 'btn'));

		$this->assertEquals('<a href="http://localhost/index.php/foo/bar">http://localhost/index.php/foo/bar</a>', $html1);
		$this->assertEquals('<a href="http://localhost/index.php/foo/bar">Foobar</a>', $html2);
		$this->assertEquals('<a href="http://localhost/index.php/foo/bar" class="btn">Foobar</a>', $html3);
	}

	/**
	 * Test generating proper listing
	 *
	 * @group laravel
	 */
	public function testGeneratingListing()
	{
		$list = array(
			'foo',
			'foobar' => array(
				'hello',
				'hello world',
			),
		);

		$html1 = HTML::ul($list);
		$html2 = HTML::ul($list, array('class' => 'nav'));
		$html3 = HTML::ol($list);
		$html4 = HTML::ol($list, array('class' => 'nav'));

		$this->assertEquals('<ul><li>foo</li><li>foobar<ul><li>hello</li><li>hello world</li></ul></li></ul>', $html1);
		$this->assertEquals('<ul class="nav"><li>foo</li><li>foobar<ul><li>hello</li><li>hello world</li></ul></li></ul>', $html2);
		$this->assertEquals('<ol><li>foo</li><li>foobar<ol><li>hello</li><li>hello world</li></ol></li></ol>', $html3);
		$this->assertEquals('<ol class="nav"><li>foo</li><li>foobar<ol><li>hello</li><li>hello world</li></ol></li></ol>', $html4);
	}

	/**
	 * Test generating proper listing
	 *
	 * @group laravel
	 */
	public function testGeneratingDefinition()
	{
		$definition = array(
			'foo' => 'foobar',
			'hello' => 'hello world',
		);

		$html1 = HTML::dl($definition);
		$html2 = HTML::dl($definition, array('class' => 'nav'));

		$this->assertEquals('<dl><dt>foo</dt><dd>foobar</dd><dt>hello</dt><dd>hello world</dd></dl>', $html1);
		$this->assertEquals('<dl class="nav"><dt>foo</dt><dd>foobar</dd><dt>hello</dt><dd>hello world</dd></dl>', $html2);
	}

	/**
	 * Test generating proper image link
	 *
	 * @group laravel
	 */
	public function testGeneratingAssetLinkImage()
	{
		$html1 = HTML::image('foo.jpg');
		$html2 = HTML::image('foo.jpg', 'Foobar');
		$html3 = HTML::image('foo.jpg', 'Foobar', array('class' => 'btn'));
		$html4 = HTML::image('http://google.com/images.jpg', 'Google');

		$this->assertEquals('<img src="http://localhost/foo.jpg" alt="">', $html1);
		$this->assertEquals('<img src="http://localhost/foo.jpg" alt="Foobar">', $html2);
		$this->assertEquals('<img src="http://localhost/foo.jpg" class="btn" alt="Foobar">', $html3);
		$this->assertEquals('<img src="http://google.com/images.jpg" alt="Google">', $html4);
	}
}