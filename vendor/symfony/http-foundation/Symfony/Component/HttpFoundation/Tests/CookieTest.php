<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests;

use Symfony\Component\HttpFoundation\Cookie;

/**
 * CookieTest
 *
 * @author John Kary <john@johnkary.net>
 * @author Hugo Hamon <hugo.hamon@sensio.com>
 */
class CookieTest extends \PHPUnit_Framework_TestCase
{
    public function invalidNames()
    {
        return array(
            array(''),
            array(",MyName"),
            array(";MyName"),
            array(" MyName"),
            array("\tMyName"),
            array("\rMyName"),
            array("\nMyName"),
            array("\013MyName"),
            array("\014MyName"),
        );
    }

    /**
     * @dataProvider invalidNames
     * @expectedException \InvalidArgumentException
     * @covers Symfony\Component\HttpFoundation\Cookie::__construct
     */
    public function testInstantiationThrowsExceptionIfCookieNameContainsInvalidCharacters($name)
    {
        new Cookie($name);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidExpiration()
    {
        $cookie = new Cookie('MyCookie', 'foo', 'bar');
    }

    /**
     * @covers Symfony\Component\HttpFoundation\Cookie::getValue
     */
    public function testGetValue()
    {
        $value = 'MyValue';
        $cookie = new Cookie('MyCookie', $value);

        $this->assertSame($value, $cookie->getValue(), '->getValue() returns the proper value');
    }

    public function testGetPath()
    {
        $cookie = new Cookie('foo', 'bar');

        $this->assertSame('/', $cookie->getPath(), '->getPath() returns / as the default path');
    }

    public function testGetExpiresTime()
    {
        $cookie = new Cookie('foo', 'bar', 3600);

        $this->assertEquals(3600, $cookie->getExpiresTime(), '->getExpiresTime() returns the expire date');
    }

    public function testConstructorWithDateTime()
    {
        $expire = new \DateTime();
        $cookie = new Cookie('foo', 'bar', $expire);

        $this->assertEquals($expire->format('U'), $cookie->getExpiresTime(), '->getExpiresTime() returns the expire date');
    }

    public function testGetExpiresTimeWithStringValue()
    {
        $value = "+1 day";
        $cookie = new Cookie('foo', 'bar', $value);
        $expire = strtotime($value);

        $this->assertEquals($expire, $cookie->getExpiresTime(), '->getExpiresTime() returns the expire date');
    }

    public function testGetDomain()
    {
        $cookie = new Cookie('foo', 'bar', 3600, '/', '.myfoodomain.com');

        $this->assertEquals('.myfoodomain.com', $cookie->getDomain(), '->getDomain() returns the domain name on which the cookie is valid');
    }

    public function testIsSecure()
    {
        $cookie = new Cookie('foo', 'bar', 3600, '/', '.myfoodomain.com', true);

        $this->assertTrue($cookie->isSecure(), '->isSecure() returns whether the cookie is transmitted over HTTPS');
    }

    public function testIsHttpOnly()
    {
        $cookie = new Cookie('foo', 'bar', 3600, '/', '.myfoodomain.com', false, true);

        $this->assertTrue($cookie->isHttpOnly(), '->isHttpOnly() returns whether the cookie is only transmitted over HTTP');
    }

    public function testCookieIsNotCleared()
    {
        $cookie = new Cookie('foo', 'bar', time()+3600*24);

        $this->assertFalse($cookie->isCleared(), '->isCleared() returns false if the cookie did not expire yet');
    }

    public function testCookieIsCleared()
    {
        $cookie = new Cookie('foo', 'bar', time()-20);

        $this->assertTrue($cookie->isCleared(), '->isCleared() returns true if the cookie has expired');
    }

    public function testToString()
    {
        $cookie = new Cookie('foo', 'bar', strtotime('Fri, 20-May-2011 15:25:52 GMT'), '/', '.myfoodomain.com', true);
        $this->assertEquals('foo=bar; expires=Fri, 20-May-2011 15:25:52 GMT; path=/; domain=.myfoodomain.com; secure; httponly', $cookie->__toString(), '->__toString() returns string representation of the cookie');

        $cookie = new Cookie('foo', null, 1, '/admin/', '.myfoodomain.com');
        $this->assertEquals('foo=deleted; expires='.gmdate("D, d-M-Y H:i:s T", time()-31536001).'; path=/admin/; domain=.myfoodomain.com; httponly', $cookie->__toString(), '->__toString() returns string representation of a cleared cookie if value is NULL');

        $cookie = new Cookie('foo', 'bar', 0, '/', '');
        $this->assertEquals('foo=bar; path=/; httponly', $cookie->__toString());
    }
}
