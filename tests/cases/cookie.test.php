<?php namespace Laravel;

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
	 * Test Cookie::has method.
	 *
	 * @group laravel
	 */
	public function testHasMethodIndicatesIfCookieInSet()
	{
		Cookie::$jar['foo'] = array('value' => 'bar');
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
		Cookie::$jar['foo'] = array('value' => 'bar');
		$this->assertEquals('bar', Cookie::get('foo'));

		Cookie::put('bar', 'baz');
		$this->assertEquals('baz', Cookie::get('bar'));
	}

	/**
	 * Test the Cookie::get method respects signatures.
	 *
	 * @group laravel
	 */
	public function testTamperedCookiesAreReturnedAsNull()
	{
		$_COOKIE['foo'] = Cookie::sign('foo', 'bar');
		$this->assertEquals('bar', Cookie::get('foo'));

		$_COOKIE['foo'] .= '-baz';
		$this->assertNull(Cookie::get('foo'));

		$_COOKIE['foo'] = Cookie::sign('foo', 'bar');
		$_COOKIE['foo'] = 'aslk'.$_COOKIE['foo'];
		$this->assertNull(Cookie::get('foo'));
	}

	/**
	 * Test Cookie::forever method.
	 *
	 * @group laravel
	 */
	public function testForeverShouldUseATonOfMinutes()
	{
		Cookie::forever('foo', 'bar');
		$this->assertEquals('bar', Cookie::$jar['foo']['value']);
		$this->assertEquals(525600, Cookie::$jar['foo']['minutes']);

		Cookie::forever('bar', 'baz', 'path', 'domain', true);
		$this->assertEquals('path', Cookie::$jar['bar']['path']);
		$this->assertEquals('domain', Cookie::$jar['bar']['domain']);
		$this->assertTrue(Cookie::$jar['bar']['secure']);
	}

	/**
	 * Test the Cookie::forget method.
	 *
	 * @group laravel
	 */
	public function testForgetSetsCookieWithExpiration()
	{
		Cookie::forget('bar', 'path', 'domain', true);
		$this->assertEquals(-2000, Cookie::$jar['bar']['minutes']);
		$this->assertEquals('path', Cookie::$jar['bar']['path']);
		$this->assertEquals('domain', Cookie::$jar['bar']['domain']);
		$this->assertTrue(Cookie::$jar['bar']['secure']);
	}

	/**
	 * Test the Cookie::send method.
	 *
	 * @group laravel
	 */
	public function testSendMethodSetsProperValuesOnCookie()
	{
		$_SERVER['cookie.stub'] = array();
		$_SERVER['function.headers_sent'] = false;

		Cookie::send();
		$this->assertTrue(count($_SERVER['cookie.stub']) == 0);

		Cookie::put('foo', 'bar', 20, 'path', 'domain', true);
		Cookie::send();
		$this->assertTrue(count($_SERVER['cookie.stub']) == 1);
		$this->assertEquals('foo', $_SERVER['cookie.stub']['foo']['name']);
		$this->assertEquals(Cookie::sign('foo', 'bar'), $_SERVER['cookie.stub']['foo']['value']);
		$this->assertEquals('path', $_SERVER['cookie.stub']['foo']['path']);
		$this->assertEquals('domain', $_SERVER['cookie.stub']['foo']['domain']);
		$this->assertEquals((time() + (20 * 60)), $_SERVER['cookie.stub']['foo']['time']);
		$this->assertTrue($_SERVER['cookie.stub']['foo']['secure']);

		Cookie::put('bar', 'baz', 0);
		Cookie::send();
		$this->assertEquals(0, $_SERVER['cookie.stub']['bar']['time']);
	}

}