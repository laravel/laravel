<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\BrowserKit\Tests;

use Symfony\Component\BrowserKit\Cookie;

class CookieTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getTestsForToFromString
     */
    public function testToFromString($cookie, $url = null)
    {
        $this->assertEquals($cookie, (string) Cookie::fromString($cookie, $url));
    }

    public function getTestsForToFromString()
    {
        return array(
            array('foo=bar; path=/'),
            array('foo=bar; path=/foo'),
            array('foo=bar; domain=google.com; path=/'),
            array('foo=bar; domain=example.com; path=/; secure', 'https://example.com/'),
            array('foo=bar; path=/; httponly'),
            array('foo=bar; domain=google.com; path=/foo; secure; httponly', 'https://google.com/'),
            array('foo=bar=baz; path=/'),
            array('foo=bar%3Dbaz; path=/'),
        );
    }

    public function testFromStringIgnoreSecureFlag()
    {
        $this->assertFalse(Cookie::fromString('foo=bar; secure')->isSecure());
        $this->assertFalse(Cookie::fromString('foo=bar; secure', 'http://example.com/')->isSecure());
    }

    /**
     * @dataProvider getExpireCookieStrings
     */
    public function testFromStringAcceptsSeveralExpiresDateFormats($cookie)
    {
        $this->assertEquals(1596185377, Cookie::fromString($cookie)->getExpiresTime());
    }

    public function getExpireCookieStrings()
    {
        return array(
            array('foo=bar; expires=Fri, 31-Jul-2020 08:49:37 GMT'),
            array('foo=bar; expires=Fri, 31 Jul 2020 08:49:37 GMT'),
            array('foo=bar; expires=Fri, 31-07-2020 08:49:37 GMT'),
            array('foo=bar; expires=Fri, 31-07-20 08:49:37 GMT'),
            array('foo=bar; expires=Friday, 31-Jul-20 08:49:37 GMT'),
            array('foo=bar; expires=Fri Jul 31 08:49:37 2020'),
            array('foo=bar; expires=\'Fri Jul 31 08:49:37 2020\''),
            array('foo=bar; expires=Friday July 31st 2020, 08:49:37 GMT'),
        );
    }

    public function testFromStringWithCapitalization()
    {
        $this->assertEquals('Foo=Bar; path=/', (string) Cookie::fromString('Foo=Bar'));
        $this->assertEquals('foo=bar; expires=Fri, 31 Dec 2010 23:59:59 GMT; path=/', (string) Cookie::fromString('foo=bar; Expires=Fri, 31 Dec 2010 23:59:59 GMT'));
        $this->assertEquals('foo=bar; domain=www.example.org; path=/; httponly', (string) Cookie::fromString('foo=bar; DOMAIN=www.example.org; HttpOnly'));
    }

    public function testFromStringWithUrl()
    {
        $this->assertEquals('foo=bar; domain=www.example.com; path=/', (string) Cookie::FromString('foo=bar', 'http://www.example.com/'));
        $this->assertEquals('foo=bar; domain=www.example.com; path=/', (string) Cookie::FromString('foo=bar', 'http://www.example.com'));
        $this->assertEquals('foo=bar; domain=www.example.com; path=/', (string) Cookie::FromString('foo=bar', 'http://www.example.com?foo'));
        $this->assertEquals('foo=bar; domain=www.example.com; path=/foo', (string) Cookie::FromString('foo=bar', 'http://www.example.com/foo/bar'));
        $this->assertEquals('foo=bar; domain=www.example.com; path=/', (string) Cookie::FromString('foo=bar; path=/', 'http://www.example.com/foo/bar'));
        $this->assertEquals('foo=bar; domain=www.myotherexample.com; path=/', (string) Cookie::FromString('foo=bar; domain=www.myotherexample.com', 'http://www.example.com/'));
    }

    public function testFromStringThrowsAnExceptionIfCookieIsNotValid()
    {
        $this->setExpectedException('InvalidArgumentException');
        Cookie::FromString('foo');
    }

    public function testFromStringThrowsAnExceptionIfCookieDateIsNotValid()
    {
        $this->setExpectedException('InvalidArgumentException');
        Cookie::FromString('foo=bar; expires=Flursday July 31st 2020, 08:49:37 GMT');
    }

    public function testFromStringThrowsAnExceptionIfUrlIsNotValid()
    {
        $this->setExpectedException('InvalidArgumentException');
        Cookie::FromString('foo=bar', 'foobar');
    }

    public function testGetName()
    {
        $cookie = new Cookie('foo', 'bar');
        $this->assertEquals('foo', $cookie->getName(), '->getName() returns the cookie name');
    }

    public function testGetValue()
    {
        $cookie = new Cookie('foo', 'bar');
        $this->assertEquals('bar', $cookie->getValue(), '->getValue() returns the cookie value');

        $cookie = new Cookie('foo', 'bar%3Dbaz', null, '/', '', false, true, true); // raw value
        $this->assertEquals('bar=baz', $cookie->getValue(), '->getValue() returns the urldecoded cookie value');
    }

    public function testGetRawValue()
    {
        $cookie = new Cookie('foo', 'bar=baz'); // decoded value
        $this->assertEquals('bar%3Dbaz', $cookie->getRawValue(), '->getRawValue() returns the urlencoded cookie value');
        $cookie = new Cookie('foo', 'bar%3Dbaz', null, '/', '', false, true, true); // raw value
        $this->assertEquals('bar%3Dbaz', $cookie->getRawValue(), '->getRawValue() returns the non-urldecoded cookie value');
    }

    public function testGetPath()
    {
        $cookie = new Cookie('foo', 'bar', 0);
        $this->assertEquals('/', $cookie->getPath(), '->getPath() returns / is no path is defined');

        $cookie = new Cookie('foo', 'bar', 0, '/foo');
        $this->assertEquals('/foo', $cookie->getPath(), '->getPath() returns the cookie path');
    }

    public function testGetDomain()
    {
        $cookie = new Cookie('foo', 'bar', 0, '/', 'foo.com');
        $this->assertEquals('foo.com', $cookie->getDomain(), '->getDomain() returns the cookie domain');
    }

    public function testIsSecure()
    {
        $cookie = new Cookie('foo', 'bar');
        $this->assertFalse($cookie->isSecure(), '->isSecure() returns false if not defined');

        $cookie = new Cookie('foo', 'bar', 0, '/', 'foo.com', true);
        $this->assertTrue($cookie->isSecure(), '->isSecure() returns the cookie secure flag');
    }

    public function testIsHttponly()
    {
        $cookie = new Cookie('foo', 'bar');
        $this->assertTrue($cookie->isHttpOnly(), '->isHttpOnly() returns false if not defined');

        $cookie = new Cookie('foo', 'bar', 0, '/', 'foo.com', false, true);
        $this->assertTrue($cookie->isHttpOnly(), '->isHttpOnly() returns the cookie httponly flag');
    }

    public function testGetExpiresTime()
    {
        $cookie = new Cookie('foo', 'bar');
        $this->assertNull($cookie->getExpiresTime(), '->getExpiresTime() returns the expires time');

        $cookie = new Cookie('foo', 'bar', $time = time() - 86400);
        $this->assertEquals($time, $cookie->getExpiresTime(), '->getExpiresTime() returns the expires time');
    }

    public function testIsExpired()
    {
        $cookie = new Cookie('foo', 'bar');
        $this->assertFalse($cookie->isExpired(), '->isExpired() returns false when the cookie never expires (null as expires time)');

        $cookie = new Cookie('foo', 'bar', time() - 86400);
        $this->assertTrue($cookie->isExpired(), '->isExpired() returns true when the cookie is expired');

        $cookie = new Cookie('foo', 'bar', 0);
        $this->assertFalse($cookie->isExpired());
    }
}
