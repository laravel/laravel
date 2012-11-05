<?php namespace Laravel;

use Symfony\Component\HttpFoundation\LaravelRequest as RequestFoundation;

/**
 * Stub the global setcookie method into the Laravel namespace.
 */
function setcookie($name, $value, $time, $path, $domain, $secure)
{
	$_SERVER['cookie.stub'][$name] = compact('name', 'value', 'time', 'path', 'domain', 'secure');
}

function headers_sent()
{
	return $_SERVER['function.headers_sent'];
}

class CookieTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		Cookie::$jar = array();
	}

	/**
	 * Tear down the test environment.
	 */
	public function tearDown()
	{
		Cookie::$jar = array();
	}

	/**
	 * Set one of the $_SERVER variables.
	 *
	 * @param string  $key
	 * @param string  $value
	 */
	protected function setServerVar($key, $value)
	{
		$_SERVER[$key] = $value;

		$this->restartRequest();
	}

	/**
	 * Reinitialize the global request.
	 * 
	 * @return void
	 */
	protected function restartRequest()
	{
		// FIXME: Ugly hack, but old contents from previous requests seem to
		// trip up the Foundation class.
		$_FILES = array();

		Request::$foundation = RequestFoundation::createFromGlobals();
	}

	/**
	 * Test Cookie::has method.
	 *
	 * @group laravel
	 */
	public function testHasMethodIndicatesIfCookieInSet()
	{
		Cookie::$jar['foo'] = array('value' => Cookie::hash('bar').'+bar');
		$this->assertTrue(Cookie::has('foo'));
		$this->assertFalse(Cookie::has('bar'));

		Cookie::put('baz', 'foo');
		$this->assertTrue(Cookie::has('baz'));
	}

	/**
	 * Test the Cookie::get method.
	 *
	 * @group laravel
	 */
	public function testGetMethodCanReturnValueOfCookies()
	{
		Cookie::$jar['foo'] = array('value' => Cookie::hash('bar').'+bar');
		$this->assertEquals('bar', Cookie::get('foo'));

		Cookie::put('bar', 'baz');
		$this->assertEquals('baz', Cookie::get('bar'));
	}

	/**
	 * Test Cookie::forever method.
	 *
	 * @group laravel
	 */
	public function testForeverShouldUseATonOfMinutes()
	{
		Cookie::forever('foo', 'bar');
		$this->assertEquals(Cookie::hash('bar').'+bar', Cookie::$jar['foo']['value']);

		// Shouldn't be able to test this cause while we indicate -2000 seconds 
		// cookie expiration store timestamp.
		// $this->assertEquals(525600, Cookie::$jar['foo']['expiration']);

		$this->setServerVar('HTTPS', 'on');

		Cookie::forever('bar', 'baz', 'path', 'domain', true);
		$this->assertEquals('path', Cookie::$jar['bar']['path']);
		$this->assertEquals('domain', Cookie::$jar['bar']['domain']);
		$this->assertTrue(Cookie::$jar['bar']['secure']);

		$this->setServerVar('HTTPS', 'off');
	}

	/**
	 * Test the Cookie::forget method.
	 *
	 * @group laravel
	 */
	public function testForgetSetsCookieWithExpiration()
	{
		Cookie::forget('bar', 'path', 'domain');

		// Shouldn't be able to test this cause while we indicate -2000 seconds 
		// cookie expiration store timestamp.
		//$this->assertEquals(-2000, Cookie::$jar['bar']['expiration']);

		$this->assertEquals('path', Cookie::$jar['bar']['path']);
		$this->assertEquals('domain', Cookie::$jar['bar']['domain']);
		$this->assertFalse(Cookie::$jar['bar']['secure']);
	}

}