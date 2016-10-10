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

use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Symfony\Component\HttpFoundation\Request::__construct
     */
    public function testConstructor()
    {
        $this->testInitialize();
    }

    /**
     * @covers Symfony\Component\HttpFoundation\Request::initialize
     */
    public function testInitialize()
    {
        $request = new Request();

        $request->initialize(array('foo' => 'bar'));
        $this->assertEquals('bar', $request->query->get('foo'), '->initialize() takes an array of query parameters as its first argument');

        $request->initialize(array(), array('foo' => 'bar'));
        $this->assertEquals('bar', $request->request->get('foo'), '->initialize() takes an array of request parameters as its second argument');

        $request->initialize(array(), array(), array('foo' => 'bar'));
        $this->assertEquals('bar', $request->attributes->get('foo'), '->initialize() takes an array of attributes as its third argument');

        $request->initialize(array(), array(), array(), array(), array(), array('HTTP_FOO' => 'bar'));
        $this->assertEquals('bar', $request->headers->get('FOO'), '->initialize() takes an array of HTTP headers as its sixth argument');
    }

    public function testGetLocale()
    {
        $request = new Request();
        $request->setLocale('pl');
        $locale = $request->getLocale();
        $this->assertEquals('pl', $locale);
    }

    public function testGetUser()
    {
        $request = Request::create('http://user_test:password_test@test.com/');
        $user = $request->getUser();

        $this->assertEquals('user_test', $user);
    }

    public function testGetPassword()
    {
        $request = Request::create('http://user_test:password_test@test.com/');
        $password = $request->getPassword();

        $this->assertEquals('password_test', $password);
    }

    public function testIsNoCache()
    {
        $request = new Request();
        $isNoCache = $request->isNoCache();

        $this->assertFalse($isNoCache);
    }

    public function testGetContentType()
    {
        $request = new Request();
        $contentType = $request->getContentType();

        $this->assertNull($contentType);
    }

    public function testSetDefaultLocale()
    {
        $request = new Request();
        $request->setDefaultLocale('pl');
        $locale = $request->getLocale();

        $this->assertEquals('pl', $locale);
    }

    /**
     * @covers Symfony\Component\HttpFoundation\Request::create
     */
    public function testCreate()
    {
        $request = Request::create('http://test.com/foo?bar=baz');
        $this->assertEquals('http://test.com/foo?bar=baz', $request->getUri());
        $this->assertEquals('/foo', $request->getPathInfo());
        $this->assertEquals('bar=baz', $request->getQueryString());
        $this->assertEquals(80, $request->getPort());
        $this->assertEquals('test.com', $request->getHttpHost());
        $this->assertFalse($request->isSecure());

        $request = Request::create('http://test.com/foo', 'GET', array('bar' => 'baz'));
        $this->assertEquals('http://test.com/foo?bar=baz', $request->getUri());
        $this->assertEquals('/foo', $request->getPathInfo());
        $this->assertEquals('bar=baz', $request->getQueryString());
        $this->assertEquals(80, $request->getPort());
        $this->assertEquals('test.com', $request->getHttpHost());
        $this->assertFalse($request->isSecure());

        $request = Request::create('http://test.com/foo?bar=foo', 'GET', array('bar' => 'baz'));
        $this->assertEquals('http://test.com/foo?bar=baz', $request->getUri());
        $this->assertEquals('/foo', $request->getPathInfo());
        $this->assertEquals('bar=baz', $request->getQueryString());
        $this->assertEquals(80, $request->getPort());
        $this->assertEquals('test.com', $request->getHttpHost());
        $this->assertFalse($request->isSecure());

        $request = Request::create('https://test.com/foo?bar=baz');
        $this->assertEquals('https://test.com/foo?bar=baz', $request->getUri());
        $this->assertEquals('/foo', $request->getPathInfo());
        $this->assertEquals('bar=baz', $request->getQueryString());
        $this->assertEquals(443, $request->getPort());
        $this->assertEquals('test.com', $request->getHttpHost());
        $this->assertTrue($request->isSecure());

        $request = Request::create('test.com:90/foo');
        $this->assertEquals('http://test.com:90/foo', $request->getUri());
        $this->assertEquals('/foo', $request->getPathInfo());
        $this->assertEquals('test.com', $request->getHost());
        $this->assertEquals('test.com:90', $request->getHttpHost());
        $this->assertEquals(90, $request->getPort());
        $this->assertFalse($request->isSecure());

        $request = Request::create('https://test.com:90/foo');
        $this->assertEquals('https://test.com:90/foo', $request->getUri());
        $this->assertEquals('/foo', $request->getPathInfo());
        $this->assertEquals('test.com', $request->getHost());
        $this->assertEquals('test.com:90', $request->getHttpHost());
        $this->assertEquals(90, $request->getPort());
        $this->assertTrue($request->isSecure());

        $request = Request::create('https://127.0.0.1:90/foo');
        $this->assertEquals('https://127.0.0.1:90/foo', $request->getUri());
        $this->assertEquals('/foo', $request->getPathInfo());
        $this->assertEquals('127.0.0.1', $request->getHost());
        $this->assertEquals('127.0.0.1:90', $request->getHttpHost());
        $this->assertEquals(90, $request->getPort());
        $this->assertTrue($request->isSecure());

        $request = Request::create('https://[::1]:90/foo');
        $this->assertEquals('https://[::1]:90/foo', $request->getUri());
        $this->assertEquals('/foo', $request->getPathInfo());
        $this->assertEquals('[::1]', $request->getHost());
        $this->assertEquals('[::1]:90', $request->getHttpHost());
        $this->assertEquals(90, $request->getPort());
        $this->assertTrue($request->isSecure());

        $request = Request::create('https://[::1]/foo');
        $this->assertEquals('https://[::1]/foo', $request->getUri());
        $this->assertEquals('/foo', $request->getPathInfo());
        $this->assertEquals('[::1]', $request->getHost());
        $this->assertEquals('[::1]', $request->getHttpHost());
        $this->assertEquals(443, $request->getPort());
        $this->assertTrue($request->isSecure());

        $json = '{"jsonrpc":"2.0","method":"echo","id":7,"params":["Hello World"]}';
        $request = Request::create('http://example.com/jsonrpc', 'POST', array(), array(), array(), array(), $json);
        $this->assertEquals($json, $request->getContent());
        $this->assertFalse($request->isSecure());

        $request = Request::create('http://test.com');
        $this->assertEquals('http://test.com/', $request->getUri());
        $this->assertEquals('/', $request->getPathInfo());
        $this->assertEquals('', $request->getQueryString());
        $this->assertEquals(80, $request->getPort());
        $this->assertEquals('test.com', $request->getHttpHost());
        $this->assertFalse($request->isSecure());

        $request = Request::create('http://test.com?test=1');
        $this->assertEquals('http://test.com/?test=1', $request->getUri());
        $this->assertEquals('/', $request->getPathInfo());
        $this->assertEquals('test=1', $request->getQueryString());
        $this->assertEquals(80, $request->getPort());
        $this->assertEquals('test.com', $request->getHttpHost());
        $this->assertFalse($request->isSecure());

        $request = Request::create('http://test.com:90/?test=1');
        $this->assertEquals('http://test.com:90/?test=1', $request->getUri());
        $this->assertEquals('/', $request->getPathInfo());
        $this->assertEquals('test=1', $request->getQueryString());
        $this->assertEquals(90, $request->getPort());
        $this->assertEquals('test.com:90', $request->getHttpHost());
        $this->assertFalse($request->isSecure());

        $request = Request::create('http://username:password@test.com');
        $this->assertEquals('http://test.com/', $request->getUri());
        $this->assertEquals('/', $request->getPathInfo());
        $this->assertEquals('', $request->getQueryString());
        $this->assertEquals(80, $request->getPort());
        $this->assertEquals('test.com', $request->getHttpHost());
        $this->assertEquals('username', $request->getUser());
        $this->assertEquals('password', $request->getPassword());
        $this->assertFalse($request->isSecure());

        $request = Request::create('http://username@test.com');
        $this->assertEquals('http://test.com/', $request->getUri());
        $this->assertEquals('/', $request->getPathInfo());
        $this->assertEquals('', $request->getQueryString());
        $this->assertEquals(80, $request->getPort());
        $this->assertEquals('test.com', $request->getHttpHost());
        $this->assertEquals('username', $request->getUser());
        $this->assertSame('',$request->getPassword());
        $this->assertFalse($request->isSecure());

        $request = Request::create('http://test.com/?foo');
        $this->assertEquals('/?foo', $request->getRequestUri());
        $this->assertEquals(array('foo' => ''), $request->query->all());
    }

    /**
     * @covers Symfony\Component\HttpFoundation\Request::create
     */
    public function testCreateCheckPrecedence()
    {
        // server is used by default
        $request = Request::create('/', 'DELETE', array(), array(), array(), array(
            'HTTP_HOST'     => 'example.com',
            'HTTPS'         => 'on',
            'SERVER_PORT'   => 443,
            'PHP_AUTH_USER' => 'fabien',
            'PHP_AUTH_PW'   => 'pa$$',
            'QUERY_STRING'  => 'foo=bar',
            'CONTENT_TYPE'  => 'application/json',
        ));
        $this->assertEquals('example.com', $request->getHost());
        $this->assertEquals(443, $request->getPort());
        $this->assertTrue($request->isSecure());
        $this->assertEquals('fabien', $request->getUser());
        $this->assertEquals('pa$$', $request->getPassword());
        $this->assertEquals('', $request->getQueryString());
        $this->assertEquals('application/json', $request->headers->get('CONTENT_TYPE'));

        // URI has precedence over server
        $request = Request::create('http://thomas:pokemon@example.net:8080/?foo=bar', 'GET', array(), array(), array(), array(
            'HTTP_HOST'   => 'example.com',
            'HTTPS'       => 'on',
            'SERVER_PORT' => 443,
        ));
        $this->assertEquals('example.net', $request->getHost());
        $this->assertEquals(8080, $request->getPort());
        $this->assertFalse($request->isSecure());
        $this->assertEquals('thomas', $request->getUser());
        $this->assertEquals('pokemon', $request->getPassword());
        $this->assertEquals('foo=bar', $request->getQueryString());
    }

    public function testDuplicate()
    {
        $request = new Request(array('foo' => 'bar'), array('foo' => 'bar'), array('foo' => 'bar'), array(), array(), array('HTTP_FOO' => 'bar'));
        $dup = $request->duplicate();

        $this->assertEquals($request->query->all(), $dup->query->all(), '->duplicate() duplicates a request an copy the current query parameters');
        $this->assertEquals($request->request->all(), $dup->request->all(), '->duplicate() duplicates a request an copy the current request parameters');
        $this->assertEquals($request->attributes->all(), $dup->attributes->all(), '->duplicate() duplicates a request an copy the current attributes');
        $this->assertEquals($request->headers->all(), $dup->headers->all(), '->duplicate() duplicates a request an copy the current HTTP headers');

        $dup = $request->duplicate(array('foo' => 'foobar'), array('foo' => 'foobar'), array('foo' => 'foobar'), array(), array(), array('HTTP_FOO' => 'foobar'));

        $this->assertEquals(array('foo' => 'foobar'), $dup->query->all(), '->duplicate() overrides the query parameters if provided');
        $this->assertEquals(array('foo' => 'foobar'), $dup->request->all(), '->duplicate() overrides the request parameters if provided');
        $this->assertEquals(array('foo' => 'foobar'), $dup->attributes->all(), '->duplicate() overrides the attributes if provided');
        $this->assertEquals(array('foo' => array('foobar')), $dup->headers->all(), '->duplicate() overrides the HTTP header if provided');
    }

    public function testDuplicateWithFormat()
    {
        $request = new Request(array(), array(), array('_format' => 'json'));
        $dup = $request->duplicate();

        $this->assertEquals('json', $dup->getRequestFormat());
        $this->assertEquals('json', $dup->attributes->get('_format'));

        $request = new Request();
        $request->setRequestFormat('xml');
        $dup = $request->duplicate();

        $this->assertEquals('xml', $dup->getRequestFormat());
    }

    /**
     * @covers Symfony\Component\HttpFoundation\Request::getFormat
     * @covers Symfony\Component\HttpFoundation\Request::setFormat
     * @dataProvider getFormatToMimeTypeMapProvider
     */
    public function testGetFormatFromMimeType($format, $mimeTypes)
    {
        $request = new Request();
        foreach ($mimeTypes as $mime) {
            $this->assertEquals($format, $request->getFormat($mime));
        }
        $request->setFormat($format, $mimeTypes);
        foreach ($mimeTypes as $mime) {
            $this->assertEquals($format, $request->getFormat($mime));
        }
    }

    /**
     * @covers Symfony\Component\HttpFoundation\Request::getFormat
     */
    public function testGetFormatFromMimeTypeWithParameters()
    {
        $request = new Request();
        $this->assertEquals('json', $request->getFormat('application/json; charset=utf-8'));
    }

    /**
     * @covers Symfony\Component\HttpFoundation\Request::getMimeType
     * @dataProvider getFormatToMimeTypeMapProvider
     */
    public function testGetMimeTypeFromFormat($format, $mimeTypes)
    {
        if (null !== $format) {
            $request = new Request();
            $this->assertEquals($mimeTypes[0], $request->getMimeType($format));
        }
    }

    public function getFormatToMimeTypeMapProvider()
    {
        return array(
            array(null, array(null, 'unexistent-mime-type')),
            array('txt', array('text/plain')),
            array('js', array('application/javascript', 'application/x-javascript', 'text/javascript')),
            array('css', array('text/css')),
            array('json', array('application/json', 'application/x-json')),
            array('xml', array('text/xml', 'application/xml', 'application/x-xml')),
            array('rdf', array('application/rdf+xml')),
            array('atom',array('application/atom+xml')),
        );
    }

    /**
     * @covers Symfony\Component\HttpFoundation\Request::getUri
     */
    public function testGetUri()
    {
        $server = array();

        // Standard Request on non default PORT
        // http://host:8080/index.php/path/info?query=string

        $server['HTTP_HOST'] = 'host:8080';
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '8080';

        $server['QUERY_STRING'] = 'query=string';
        $server['REQUEST_URI'] = '/index.php/path/info?query=string';
        $server['SCRIPT_NAME'] = '/index.php';
        $server['PATH_INFO'] = '/path/info';
        $server['PATH_TRANSLATED'] = 'redirect:/index.php/path/info';
        $server['PHP_SELF'] = '/index_dev.php/path/info';
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';

        $request = new Request();

        $request->initialize(array(), array(), array(), array(), array(), $server);

        $this->assertEquals('http://host:8080/index.php/path/info?query=string', $request->getUri(), '->getUri() with non default port');

        // Use std port number
        $server['HTTP_HOST'] = 'host';
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '80';

        $request->initialize(array(), array(), array(), array(), array(), $server);

        $this->assertEquals('http://host/index.php/path/info?query=string', $request->getUri(), '->getUri() with default port');

        // Without HOST HEADER
        unset($server['HTTP_HOST']);
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '80';

        $request->initialize(array(), array(), array(), array(), array(), $server);

        $this->assertEquals('http://servername/index.php/path/info?query=string', $request->getUri(), '->getUri() with default port without HOST_HEADER');

        // Request with URL REWRITING (hide index.php)
        //   RewriteCond %{REQUEST_FILENAME} !-f
        //   RewriteRule ^(.*)$ index.php [QSA,L]
        // http://host:8080/path/info?query=string
        $server = array();
        $server['HTTP_HOST'] = 'host:8080';
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '8080';

        $server['REDIRECT_QUERY_STRING'] = 'query=string';
        $server['REDIRECT_URL'] = '/path/info';
        $server['SCRIPT_NAME'] = '/index.php';
        $server['QUERY_STRING'] = 'query=string';
        $server['REQUEST_URI'] = '/path/info?toto=test&1=1';
        $server['SCRIPT_NAME'] = '/index.php';
        $server['PHP_SELF'] = '/index.php';
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';

        $request->initialize(array(), array(), array(), array(), array(), $server);
        $this->assertEquals('http://host:8080/path/info?query=string', $request->getUri(), '->getUri() with rewrite');

        // Use std port number
        //  http://host/path/info?query=string
        $server['HTTP_HOST'] = 'host';
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '80';

        $request->initialize(array(), array(), array(), array(), array(), $server);

        $this->assertEquals('http://host/path/info?query=string', $request->getUri(), '->getUri() with rewrite and default port');

        // Without HOST HEADER
        unset($server['HTTP_HOST']);
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '80';

        $request->initialize(array(), array(), array(), array(), array(), $server);

        $this->assertEquals('http://servername/path/info?query=string', $request->getUri(), '->getUri() with rewrite, default port without HOST_HEADER');

        // With encoded characters

        $server = array(
            'HTTP_HOST'       => 'host:8080',
            'SERVER_NAME'     => 'servername',
            'SERVER_PORT'     => '8080',
            'QUERY_STRING'    => 'query=string',
            'REQUEST_URI'     => '/ba%20se/index_dev.php/foo%20bar/in+fo?query=string',
            'SCRIPT_NAME'     => '/ba se/index_dev.php',
            'PATH_TRANSLATED' => 'redirect:/index.php/foo bar/in+fo',
            'PHP_SELF'        => '/ba se/index_dev.php/path/info',
            'SCRIPT_FILENAME' => '/some/where/ba se/index_dev.php',
        );

        $request->initialize(array(), array(), array(), array(), array(), $server);

        $this->assertEquals(
            'http://host:8080/ba%20se/index_dev.php/foo%20bar/in+fo?query=string',
            $request->getUri()
        );

        // with user info

        $server['PHP_AUTH_USER'] = 'fabien';
        $request->initialize(array(), array(), array(), array(), array(), $server);
        $this->assertEquals('http://host:8080/ba%20se/index_dev.php/foo%20bar/in+fo?query=string', $request->getUri());

        $server['PHP_AUTH_PW'] = 'symfony';
        $request->initialize(array(), array(), array(), array(), array(), $server);
        $this->assertEquals('http://host:8080/ba%20se/index_dev.php/foo%20bar/in+fo?query=string', $request->getUri());
    }

    /**
     * @covers Symfony\Component\HttpFoundation\Request::getUriForPath
     */
    public function testGetUriForPath()
    {
        $request = Request::create('http://test.com/foo?bar=baz');
        $this->assertEquals('http://test.com/some/path', $request->getUriForPath('/some/path'));

        $request = Request::create('http://test.com:90/foo?bar=baz');
        $this->assertEquals('http://test.com:90/some/path', $request->getUriForPath('/some/path'));

        $request = Request::create('https://test.com/foo?bar=baz');
        $this->assertEquals('https://test.com/some/path', $request->getUriForPath('/some/path'));

        $request = Request::create('https://test.com:90/foo?bar=baz');
        $this->assertEquals('https://test.com:90/some/path', $request->getUriForPath('/some/path'));

        $server = array();

        // Standard Request on non default PORT
        // http://host:8080/index.php/path/info?query=string

        $server['HTTP_HOST'] = 'host:8080';
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '8080';

        $server['QUERY_STRING'] = 'query=string';
        $server['REQUEST_URI'] = '/index.php/path/info?query=string';
        $server['SCRIPT_NAME'] = '/index.php';
        $server['PATH_INFO'] = '/path/info';
        $server['PATH_TRANSLATED'] = 'redirect:/index.php/path/info';
        $server['PHP_SELF'] = '/index_dev.php/path/info';
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';

        $request = new Request();

        $request->initialize(array(), array(), array(), array(), array(),$server);

        $this->assertEquals('http://host:8080/index.php/some/path', $request->getUriForPath('/some/path'), '->getUriForPath() with non default port');

        // Use std port number
        $server['HTTP_HOST'] = 'host';
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '80';

        $request->initialize(array(), array(), array(), array(), array(), $server);

        $this->assertEquals('http://host/index.php/some/path', $request->getUriForPath('/some/path'), '->getUriForPath() with default port');

        // Without HOST HEADER
        unset($server['HTTP_HOST']);
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '80';

        $request->initialize(array(), array(), array(), array(), array(), $server);

        $this->assertEquals('http://servername/index.php/some/path', $request->getUriForPath('/some/path'), '->getUriForPath() with default port without HOST_HEADER');

        // Request with URL REWRITING (hide index.php)
        //   RewriteCond %{REQUEST_FILENAME} !-f
        //   RewriteRule ^(.*)$ index.php [QSA,L]
        // http://host:8080/path/info?query=string
        $server = array();
        $server['HTTP_HOST'] = 'host:8080';
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '8080';

        $server['REDIRECT_QUERY_STRING'] = 'query=string';
        $server['REDIRECT_URL'] = '/path/info';
        $server['SCRIPT_NAME'] = '/index.php';
        $server['QUERY_STRING'] = 'query=string';
        $server['REQUEST_URI'] = '/path/info?toto=test&1=1';
        $server['SCRIPT_NAME'] = '/index.php';
        $server['PHP_SELF'] = '/index.php';
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';

        $request->initialize(array(), array(), array(), array(), array(), $server);
        $this->assertEquals('http://host:8080/some/path', $request->getUriForPath('/some/path'), '->getUri() with rewrite');

        // Use std port number
        //  http://host/path/info?query=string
        $server['HTTP_HOST'] = 'host';
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '80';

        $request->initialize(array(), array(), array(), array(), array(), $server);

        $this->assertEquals('http://host/some/path', $request->getUriForPath('/some/path'), '->getUriForPath() with rewrite and default port');

        // Without HOST HEADER
        unset($server['HTTP_HOST']);
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '80';

        $request->initialize(array(), array(), array(), array(), array(), $server);

        $this->assertEquals('http://servername/some/path', $request->getUriForPath('/some/path'), '->getUriForPath() with rewrite, default port without HOST_HEADER');
        $this->assertEquals('servername', $request->getHttpHost());

        // with user info

        $server['PHP_AUTH_USER'] = 'fabien';
        $request->initialize(array(), array(), array(), array(), array(), $server);
        $this->assertEquals('http://servername/some/path', $request->getUriForPath('/some/path'));

        $server['PHP_AUTH_PW'] = 'symfony';
        $request->initialize(array(), array(), array(), array(), array(), $server);
        $this->assertEquals('http://servername/some/path', $request->getUriForPath('/some/path'));
    }

    /**
     * @covers Symfony\Component\HttpFoundation\Request::getUserInfo
     */
    public function testGetUserInfo()
    {
        $request = new Request();

        $server['PHP_AUTH_USER'] = 'fabien';
        $request->initialize(array(), array(), array(), array(), array(), $server);
        $this->assertEquals('fabien', $request->getUserInfo());

        $server['PHP_AUTH_USER'] = '0';
        $request->initialize(array(), array(), array(), array(), array(), $server);
        $this->assertEquals('0', $request->getUserInfo());

        $server['PHP_AUTH_PW'] = '0';
        $request->initialize(array(), array(), array(), array(), array(), $server);
        $this->assertEquals('0:0', $request->getUserInfo());
    }

    /**
     * @covers Symfony\Component\HttpFoundation\Request::getSchemeAndHttpHost
     */
    public function testGetSchemeAndHttpHost()
    {
        $request = new Request();

        $server = array();
        $server['SERVER_NAME'] = 'servername';
        $server['SERVER_PORT'] = '90';
        $request->initialize(array(), array(), array(), array(), array(), $server);
        $this->assertEquals('http://servername:90', $request->getSchemeAndHttpHost());

        $server['PHP_AUTH_USER'] = 'fabien';
        $request->initialize(array(), array(), array(), array(), array(), $server);
        $this->assertEquals('http://servername:90', $request->getSchemeAndHttpHost());

        $server['PHP_AUTH_USER'] = '0';
        $request->initialize(array(), array(), array(), array(), array(), $server);
        $this->assertEquals('http://servername:90', $request->getSchemeAndHttpHost());

        $server['PHP_AUTH_PW'] = '0';
        $request->initialize(array(), array(), array(), array(), array(), $server);
        $this->assertEquals('http://servername:90', $request->getSchemeAndHttpHost());
    }

    /**
     * @covers Symfony\Component\HttpFoundation\Request::getQueryString
     * @covers Symfony\Component\HttpFoundation\Request::normalizeQueryString
     * @dataProvider getQueryStringNormalizationData
     */
    public function testGetQueryString($query, $expectedQuery, $msg)
    {
        $request = new Request();

        $request->server->set('QUERY_STRING', $query);
        $this->assertSame($expectedQuery, $request->getQueryString(), $msg);
    }

    public function getQueryStringNormalizationData()
    {
        return array(
            array('foo', 'foo', 'works with valueless parameters'),
            array('foo=', 'foo=', 'includes a dangling equal sign'),
            array('bar=&foo=bar', 'bar=&foo=bar', '->works with empty parameters'),
            array('foo=bar&bar=', 'bar=&foo=bar', 'sorts keys alphabetically'),

            // GET parameters, that are submitted from a HTML form, encode spaces as "+" by default (as defined in enctype application/x-www-form-urlencoded).
            // PHP also converts "+" to spaces when filling the global _GET or when using the function parse_str.
            array('him=John%20Doe&her=Jane+Doe', 'her=Jane%20Doe&him=John%20Doe', 'normalizes spaces in both encodings "%20" and "+"'),

            array('foo[]=1&foo[]=2', 'foo%5B%5D=1&foo%5B%5D=2', 'allows array notation'),
            array('foo=1&foo=2', 'foo=1&foo=2', 'allows repeated parameters'),
            array('pa%3Dram=foo%26bar%3Dbaz&test=test', 'pa%3Dram=foo%26bar%3Dbaz&test=test', 'works with encoded delimiters'),
            array('0', '0', 'allows "0"'),
            array('Jane Doe&John%20Doe', 'Jane%20Doe&John%20Doe', 'normalizes encoding in keys'),
            array('her=Jane Doe&him=John%20Doe', 'her=Jane%20Doe&him=John%20Doe', 'normalizes encoding in values'),
            array('foo=bar&&&test&&', 'foo=bar&test', 'removes unneeded delimiters'),
            array('formula=e=m*c^2', 'formula=e%3Dm%2Ac%5E2', 'correctly treats only the first "=" as delimiter and the next as value'),

            // Ignore pairs with empty key, even if there was a value, e.g. "=value", as such nameless values cannot be retrieved anyway.
            // PHP also does not include them when building _GET.
            array('foo=bar&=a=b&=x=y', 'foo=bar', 'removes params with empty key'),
        );
    }

    public function testGetQueryStringReturnsNull()
    {
        $request = new Request();

        $this->assertNull($request->getQueryString(), '->getQueryString() returns null for non-existent query string');

        $request->server->set('QUERY_STRING', '');
        $this->assertNull($request->getQueryString(), '->getQueryString() returns null for empty query string');
    }

    public function testGetHost()
    {
        $request = new Request();

        $request->initialize(array('foo' => 'bar'));
        $this->assertEquals('', $request->getHost(), '->getHost() return empty string if not initialized');

        $request->initialize(array(), array(), array(), array(), array(), array('HTTP_HOST' => 'www.example.com'));
        $this->assertEquals('www.example.com', $request->getHost(), '->getHost() from Host Header');

        // Host header with port number
        $request->initialize(array(), array(), array(), array(), array(), array('HTTP_HOST' => 'www.example.com:8080'));
        $this->assertEquals('www.example.com', $request->getHost(), '->getHost() from Host Header with port number');

        // Server values
        $request->initialize(array(), array(), array(), array(), array(), array('SERVER_NAME' => 'www.example.com'));
        $this->assertEquals('www.example.com', $request->getHost(), '->getHost() from server name');

        $request->initialize(array(), array(), array(), array(), array(), array('SERVER_NAME' => 'www.example.com', 'HTTP_HOST' => 'www.host.com'));
        $this->assertEquals('www.host.com', $request->getHost(), '->getHost() value from Host header has priority over SERVER_NAME ');
    }

    public function testGetPort()
    {
        $request = Request::create('http://example.com', 'GET', array(), array(), array(), array(
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_PORT' => '443',
        ));
        $port = $request->getPort();

        $this->assertEquals(80, $port, 'Without trusted proxies FORWARDED_PROTO and FORWARDED_PORT are ignored.');

        Request::setTrustedProxies(array('1.1.1.1'));
        $request = Request::create('http://example.com', 'GET', array(), array(), array(), array(
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_PORT'  => '8443',
        ));
        $port = $request->getPort();

        $this->assertEquals(8443, $port, 'With PROTO and PORT set PORT takes precedence.');

        $request = Request::create('http://example.com', 'GET', array(), array(), array(), array(
            'HTTP_X_FORWARDED_PROTO' => 'https',
        ));
        $port = $request->getPort();

        $this->assertEquals(443, $port, 'With only PROTO set getPort() defaults to 443.');

        $request = Request::create('http://example.com', 'GET', array(), array(), array(), array(
            'HTTP_X_FORWARDED_PROTO' => 'http',
        ));
        $port = $request->getPort();

        $this->assertEquals(80, $port, 'If X_FORWARDED_PROTO is set to HTTP return 80.');

        $request = Request::create('http://example.com', 'GET', array(), array(), array(), array(
            'HTTP_X_FORWARDED_PROTO' => 'On',
        ));
        $port = $request->getPort();
        $this->assertEquals(443, $port, 'With only PROTO set and value is On, getPort() defaults to 443.');

        $request = Request::create('http://example.com', 'GET', array(), array(), array(), array(
            'HTTP_X_FORWARDED_PROTO' => '1',
        ));
        $port = $request->getPort();
        $this->assertEquals(443, $port, 'With only PROTO set and value is 1, getPort() defaults to 443.');

        $request = Request::create('http://example.com', 'GET', array(), array(), array(), array(
            'HTTP_X_FORWARDED_PROTO' => 'something-else',
        ));
        $port = $request->getPort();
        $this->assertEquals(80, $port, 'With only PROTO set and value is not recognized, getPort() defaults to 80.');

        Request::setTrustedProxies(array());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetHostWithFakeHttpHostValue()
    {
        $request = new Request();
        $request->initialize(array(), array(), array(), array(), array(), array('HTTP_HOST' => 'www.host.com?query=string'));
        $request->getHost();
    }

    /**
     * @covers Symfony\Component\HttpFoundation\Request::setMethod
     * @covers Symfony\Component\HttpFoundation\Request::getMethod
     */
    public function testGetSetMethod()
    {
        $request = new Request();

        $this->assertEquals('GET', $request->getMethod(), '->getMethod() returns GET if no method is defined');

        $request->setMethod('get');
        $this->assertEquals('GET', $request->getMethod(), '->getMethod() returns an uppercased string');

        $request->setMethod('PURGE');
        $this->assertEquals('PURGE', $request->getMethod(), '->getMethod() returns the method even if it is not a standard one');

        $request->setMethod('POST');
        $this->assertEquals('POST', $request->getMethod(), '->getMethod() returns the method POST if no _method is defined');

        $request->setMethod('POST');
        $request->request->set('_method', 'purge');
        $this->assertEquals('POST', $request->getMethod(), '->getMethod() does not return the method from _method if defined and POST but support not enabled');

        $request = new Request();
        $request->setMethod('POST');
        $request->request->set('_method', 'purge');

        $this->assertFalse(Request::getHttpMethodParameterOverride(), 'httpMethodParameterOverride should be disabled by default');

        Request::enableHttpMethodParameterOverride();

        $this->assertTrue(Request::getHttpMethodParameterOverride(), 'httpMethodParameterOverride should be enabled now but it is not');

        $this->assertEquals('PURGE', $request->getMethod(), '->getMethod() returns the method from _method if defined and POST');
        $this->disableHttpMethodParameterOverride();

        $request = new Request();
        $request->setMethod('POST');
        $request->query->set('_method', 'purge');
        $this->assertEquals('POST', $request->getMethod(), '->getMethod() does not return the method from _method if defined and POST but support not enabled');

        $request = new Request();
        $request->setMethod('POST');
        $request->query->set('_method', 'purge');
        Request::enableHttpMethodParameterOverride();
        $this->assertEquals('PURGE', $request->getMethod(), '->getMethod() returns the method from _method if defined and POST');
        $this->disableHttpMethodParameterOverride();

        $request = new Request();
        $request->setMethod('POST');
        $request->headers->set('X-HTTP-METHOD-OVERRIDE', 'delete');
        $this->assertEquals('DELETE', $request->getMethod(), '->getMethod() returns the method from X-HTTP-Method-Override even though _method is set if defined and POST');

        $request = new Request();
        $request->setMethod('POST');
        $request->headers->set('X-HTTP-METHOD-OVERRIDE', 'delete');
        $this->assertEquals('DELETE', $request->getMethod(), '->getMethod() returns the method from X-HTTP-Method-Override if defined and POST');
    }

    /**
     * @dataProvider testGetClientIpsProvider
     */
    public function testGetClientIp($expected, $remoteAddr, $httpForwardedFor, $trustedProxies)
    {
        $request = $this->getRequestInstanceForClientIpTests($remoteAddr, $httpForwardedFor, $trustedProxies);

        $this->assertEquals($expected[0], $request->getClientIp());

        Request::setTrustedProxies(array());
    }

    /**
     * @dataProvider testGetClientIpsProvider
     */
    public function testGetClientIps($expected, $remoteAddr, $httpForwardedFor, $trustedProxies)
    {
        $request = $this->getRequestInstanceForClientIpTests($remoteAddr, $httpForwardedFor, $trustedProxies);

        $this->assertEquals($expected, $request->getClientIps());

        Request::setTrustedProxies(array());
    }

    public function testGetClientIpsProvider()
    {
        //        $expected                   $remoteAddr                $httpForwardedFor            $trustedProxies
        return array(
            // simple IPv4
            array(array('88.88.88.88'),              '88.88.88.88',              null,                        null),
            // trust the IPv4 remote addr
            array(array('88.88.88.88'),              '88.88.88.88',              null,                        array('88.88.88.88')),

            // simple IPv6
            array(array('::1'),                      '::1',                      null,                        null),
            // trust the IPv6 remote addr
            array(array('::1'),                      '::1',                      null,                        array('::1')),

            // forwarded for with remote IPv4 addr not trusted
            array(array('127.0.0.1'),                '127.0.0.1',                '88.88.88.88',               null),
            // forwarded for with remote IPv4 addr trusted
            array(array('88.88.88.88'),              '127.0.0.1',                '88.88.88.88',               array('127.0.0.1')),
            // forwarded for with remote IPv4 and all FF addrs trusted
            array(array('88.88.88.88'),              '127.0.0.1',                '88.88.88.88',               array('127.0.0.1', '88.88.88.88')),
            // forwarded for with remote IPv4 range trusted
            array(array('88.88.88.88'),              '123.45.67.89',             '88.88.88.88',               array('123.45.67.0/24')),

            // forwarded for with remote IPv6 addr not trusted
            array(array('1620:0:1cfe:face:b00c::3'), '1620:0:1cfe:face:b00c::3', '2620:0:1cfe:face:b00c::3',  null),
            // forwarded for with remote IPv6 addr trusted
            array(array('2620:0:1cfe:face:b00c::3'), '1620:0:1cfe:face:b00c::3', '2620:0:1cfe:face:b00c::3',  array('1620:0:1cfe:face:b00c::3')),
            // forwarded for with remote IPv6 range trusted
            array(array('88.88.88.88'),              '2a01:198:603:0:396e:4789:8e99:890f', '88.88.88.88',     array('2a01:198:603:0::/65')),

            // multiple forwarded for with remote IPv4 addr trusted
            array(array('88.88.88.88', '87.65.43.21', '127.0.0.1'), '123.45.67.89', '127.0.0.1, 87.65.43.21, 88.88.88.88', array('123.45.67.89')),
            // multiple forwarded for with remote IPv4 addr and some reverse proxies trusted
            array(array('87.65.43.21', '127.0.0.1'), '123.45.67.89',             '127.0.0.1, 87.65.43.21, 88.88.88.88', array('123.45.67.89', '88.88.88.88')),
            // multiple forwarded for with remote IPv4 addr and some reverse proxies trusted but in the middle
            array(array('88.88.88.88', '127.0.0.1'), '123.45.67.89',             '127.0.0.1, 87.65.43.21, 88.88.88.88', array('123.45.67.89', '87.65.43.21')),
            // multiple forwarded for with remote IPv4 addr and all reverse proxies trusted
            array(array('127.0.0.1'),                '123.45.67.89',             '127.0.0.1, 87.65.43.21, 88.88.88.88', array('123.45.67.89', '87.65.43.21', '88.88.88.88', '127.0.0.1')),

            // multiple forwarded for with remote IPv6 addr trusted
            array(array('2620:0:1cfe:face:b00c::3', '3620:0:1cfe:face:b00c::3'), '1620:0:1cfe:face:b00c::3', '3620:0:1cfe:face:b00c::3,2620:0:1cfe:face:b00c::3', array('1620:0:1cfe:face:b00c::3')),
            // multiple forwarded for with remote IPv6 addr and some reverse proxies trusted
            array(array('3620:0:1cfe:face:b00c::3'), '1620:0:1cfe:face:b00c::3', '3620:0:1cfe:face:b00c::3,2620:0:1cfe:face:b00c::3', array('1620:0:1cfe:face:b00c::3', '2620:0:1cfe:face:b00c::3')),
            // multiple forwarded for with remote IPv4 addr and some reverse proxies trusted but in the middle
            array(array('2620:0:1cfe:face:b00c::3', '4620:0:1cfe:face:b00c::3'), '1620:0:1cfe:face:b00c::3', '4620:0:1cfe:face:b00c::3,3620:0:1cfe:face:b00c::3,2620:0:1cfe:face:b00c::3', array('1620:0:1cfe:face:b00c::3', '3620:0:1cfe:face:b00c::3')),
        );
    }

    public function testGetContentWorksTwiceInDefaultMode()
    {
        $req = new Request();
        $this->assertEquals('', $req->getContent());
        $this->assertEquals('', $req->getContent());
    }

    public function testGetContentReturnsResource()
    {
        $req = new Request();
        $retval = $req->getContent(true);
        $this->assertInternalType('resource', $retval);
        $this->assertEquals("", fread($retval, 1));
        $this->assertTrue(feof($retval));
    }

    /**
     * @expectedException \LogicException
     * @dataProvider getContentCantBeCalledTwiceWithResourcesProvider
     */
    public function testGetContentCantBeCalledTwiceWithResources($first, $second)
    {
        $req = new Request();
        $req->getContent($first);
        $req->getContent($second);
    }

    public function getContentCantBeCalledTwiceWithResourcesProvider()
    {
        return array(
            'Resource then fetch' => array(true, false),
            'Resource then resource' => array(true, true),
            'Fetch then resource' => array(false, true),
        );
    }

    public function provideOverloadedMethods()
    {
        return array(
            array('PUT'),
            array('DELETE'),
            array('PATCH'),
            array('put'),
            array('delete'),
            array('patch'),

        );
    }

    /**
     * @dataProvider provideOverloadedMethods
     */
    public function testCreateFromGlobals($method)
    {
        $normalizedMethod = strtoupper($method);

        $_GET['foo1']    = 'bar1';
        $_POST['foo2']   = 'bar2';
        $_COOKIE['foo3'] = 'bar3';
        $_FILES['foo4']  = array('bar4');
        $_SERVER['foo5'] = 'bar5';

        $request = Request::createFromGlobals();
        $this->assertEquals('bar1', $request->query->get('foo1'), '::fromGlobals() uses values from $_GET');
        $this->assertEquals('bar2', $request->request->get('foo2'), '::fromGlobals() uses values from $_POST');
        $this->assertEquals('bar3', $request->cookies->get('foo3'), '::fromGlobals() uses values from $_COOKIE');
        $this->assertEquals(array('bar4'), $request->files->get('foo4'), '::fromGlobals() uses values from $_FILES');
        $this->assertEquals('bar5', $request->server->get('foo5'), '::fromGlobals() uses values from $_SERVER');

        unset($_GET['foo1'], $_POST['foo2'], $_COOKIE['foo3'], $_FILES['foo4'], $_SERVER['foo5']);

        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $request = RequestContentProxy::createFromGlobals();
        $this->assertEquals($normalizedMethod, $request->getMethod());
        $this->assertEquals('mycontent', $request->request->get('content'));

        unset($_SERVER['REQUEST_METHOD'], $_SERVER['CONTENT_TYPE']);

        Request::createFromGlobals();
        Request::enableHttpMethodParameterOverride();
        $_POST['_method']   = $method;
        $_POST['foo6']      = 'bar6';
        $_SERVER['REQUEST_METHOD'] = 'PoSt';
        $request = Request::createFromGlobals();
        $this->assertEquals($normalizedMethod, $request->getMethod());
        $this->assertEquals('POST', $request->getRealMethod());
        $this->assertEquals('bar6', $request->request->get('foo6'));

        unset($_POST['_method'], $_POST['foo6'], $_SERVER['REQUEST_METHOD']);
        $this->disableHttpMethodParameterOverride();
    }

    public function testOverrideGlobals()
    {
        $request = new Request();
        $request->initialize(array('foo' => 'bar'));

        // as the Request::overrideGlobals really work, it erase $_SERVER, so we must backup it
        $server = $_SERVER;

        $request->overrideGlobals();

        $this->assertEquals(array('foo' => 'bar'), $_GET);

        $request->initialize(array(), array('foo' => 'bar'));
        $request->overrideGlobals();

        $this->assertEquals(array('foo' => 'bar'), $_POST);

        $this->assertArrayNotHasKey('HTTP_X_FORWARDED_PROTO', $_SERVER);

        $request->headers->set('X_FORWARDED_PROTO', 'https');

        Request::setTrustedProxies(array('1.1.1.1'));
        $this->assertTrue($request->isSecure());
        Request::setTrustedProxies(array());

        $request->overrideGlobals();

        $this->assertArrayHasKey('HTTP_X_FORWARDED_PROTO', $_SERVER);

        $request->headers->set('CONTENT_TYPE', 'multipart/form-data');
        $request->headers->set('CONTENT_LENGTH', 12345);

        $request->overrideGlobals();

        $this->assertArrayHasKey('CONTENT_TYPE', $_SERVER);
        $this->assertArrayHasKey('CONTENT_LENGTH', $_SERVER);

        $request->initialize(array('foo' => 'bar', 'baz' => 'foo'));
        $request->query->remove('baz');

        $request->overrideGlobals();

        $this->assertEquals(array('foo' => 'bar'), $_GET);
        $this->assertEquals('foo=bar', $_SERVER['QUERY_STRING']);
        $this->assertEquals('foo=bar', $request->server->get('QUERY_STRING'));

        // restore initial $_SERVER array
        $_SERVER = $server;
    }

    public function testGetScriptName()
    {
        $request = new Request();
        $this->assertEquals('', $request->getScriptName());

        $server = array();
        $server['SCRIPT_NAME'] = '/index.php';

        $request->initialize(array(), array(), array(), array(), array(), $server);

        $this->assertEquals('/index.php', $request->getScriptName());

        $server = array();
        $server['ORIG_SCRIPT_NAME'] = '/frontend.php';
        $request->initialize(array(), array(), array(), array(), array(), $server);

        $this->assertEquals('/frontend.php', $request->getScriptName());

        $server = array();
        $server['SCRIPT_NAME'] = '/index.php';
        $server['ORIG_SCRIPT_NAME'] = '/frontend.php';
        $request->initialize(array(), array(), array(), array(), array(), $server);

        $this->assertEquals('/index.php', $request->getScriptName());
    }

    public function testGetBasePath()
    {
        $request = new Request();
        $this->assertEquals('', $request->getBasePath());

        $server = array();
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';
        $request->initialize(array(), array(), array(), array(), array(), $server);
        $this->assertEquals('', $request->getBasePath());

        $server = array();
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';
        $server['SCRIPT_NAME'] = '/index.php';
        $request->initialize(array(), array(), array(), array(), array(), $server);

        $this->assertEquals('', $request->getBasePath());

        $server = array();
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';
        $server['PHP_SELF'] = '/index.php';
        $request->initialize(array(), array(), array(), array(), array(), $server);

        $this->assertEquals('', $request->getBasePath());

        $server = array();
        $server['SCRIPT_FILENAME'] = '/some/where/index.php';
        $server['ORIG_SCRIPT_NAME'] = '/index.php';
        $request->initialize(array(), array(), array(), array(), array(), $server);

        $this->assertEquals('', $request->getBasePath());
    }

    public function testGetPathInfo()
    {
        $request = new Request();
        $this->assertEquals('/', $request->getPathInfo());

        $server = array();
        $server['REQUEST_URI'] = '/path/info';
        $request->initialize(array(), array(), array(), array(), array(), $server);

        $this->assertEquals('/path/info', $request->getPathInfo());

        $server = array();
        $server['REQUEST_URI'] = '/path%20test/info';
        $request->initialize(array(), array(), array(), array(), array(), $server);

        $this->assertEquals('/path%20test/info', $request->getPathInfo());
    }

    public function testGetPreferredLanguage()
    {
        $request = new Request();
        $this->assertNull($request->getPreferredLanguage());
        $this->assertNull($request->getPreferredLanguage(array()));
        $this->assertEquals('fr', $request->getPreferredLanguage(array('fr')));
        $this->assertEquals('fr', $request->getPreferredLanguage(array('fr', 'en')));
        $this->assertEquals('en', $request->getPreferredLanguage(array('en', 'fr')));
        $this->assertEquals('fr-ch', $request->getPreferredLanguage(array('fr-ch', 'fr-fr')));

        $request = new Request();
        $request->headers->set('Accept-language', 'zh, en-us; q=0.8, en; q=0.6');
        $this->assertEquals('en', $request->getPreferredLanguage(array('en', 'en-us')));

        $request = new Request();
        $request->headers->set('Accept-language', 'zh, en-us; q=0.8, en; q=0.6');
        $this->assertEquals('en', $request->getPreferredLanguage(array('fr', 'en')));

        $request = new Request();
        $request->headers->set('Accept-language', 'zh, en-us; q=0.8');
        $this->assertEquals('en', $request->getPreferredLanguage(array('fr', 'en')));

        $request = new Request();
        $request->headers->set('Accept-language', 'zh, en-us; q=0.8, fr-fr; q=0.6, fr; q=0.5');
        $this->assertEquals('en', $request->getPreferredLanguage(array('fr', 'en')));
    }

    public function testIsXmlHttpRequest()
    {
        $request = new Request();
        $this->assertFalse($request->isXmlHttpRequest());

        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $this->assertTrue($request->isXmlHttpRequest());

        $request->headers->remove('X-Requested-With');
        $this->assertFalse($request->isXmlHttpRequest());
    }

    public function testIntlLocale()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('The intl extension is needed to run this test.');
        }

        $request = new Request();

        $request->setDefaultLocale('fr');
        $this->assertEquals('fr', $request->getLocale());
        $this->assertEquals('fr', \Locale::getDefault());

        $request->setLocale('en');
        $this->assertEquals('en', $request->getLocale());
        $this->assertEquals('en', \Locale::getDefault());

        $request->setDefaultLocale('de');
        $this->assertEquals('en', $request->getLocale());
        $this->assertEquals('en', \Locale::getDefault());
    }

    public function testGetCharsets()
    {
        $request = new Request();
        $this->assertEquals(array(), $request->getCharsets());
        $request->headers->set('Accept-Charset', 'ISO-8859-1, US-ASCII, UTF-8; q=0.8, ISO-10646-UCS-2; q=0.6');
        $this->assertEquals(array(), $request->getCharsets()); // testing caching

        $request = new Request();
        $request->headers->set('Accept-Charset', 'ISO-8859-1, US-ASCII, UTF-8; q=0.8, ISO-10646-UCS-2; q=0.6');
        $this->assertEquals(array('ISO-8859-1', 'US-ASCII', 'UTF-8', 'ISO-10646-UCS-2'), $request->getCharsets());

        $request = new Request();
        $request->headers->set('Accept-Charset', 'ISO-8859-1,utf-8;q=0.7,*;q=0.7');
        $this->assertEquals(array('ISO-8859-1', 'utf-8', '*'), $request->getCharsets());
    }

    public function testGetEncodings()
    {
        $request = new Request();
        $this->assertEquals(array(), $request->getEncodings());
        $request->headers->set('Accept-Encoding', 'gzip,deflate,sdch');
        $this->assertEquals(array(), $request->getEncodings()); // testing caching

        $request = new Request();
        $request->headers->set('Accept-Encoding', 'gzip,deflate,sdch');
        $this->assertEquals(array('gzip', 'deflate', 'sdch'), $request->getEncodings());

        $request = new Request();
        $request->headers->set('Accept-Encoding', 'gzip;q=0.4,deflate;q=0.9,compress;q=0.7');
        $this->assertEquals(array('deflate', 'compress', 'gzip'), $request->getEncodings());
    }

    public function testGetAcceptableContentTypes()
    {
        $request = new Request();
        $this->assertEquals(array(), $request->getAcceptableContentTypes());
        $request->headers->set('Accept', 'application/vnd.wap.wmlscriptc, text/vnd.wap.wml, application/vnd.wap.xhtml+xml, application/xhtml+xml, text/html, multipart/mixed, */*');
        $this->assertEquals(array(), $request->getAcceptableContentTypes()); // testing caching

        $request = new Request();
        $request->headers->set('Accept', 'application/vnd.wap.wmlscriptc, text/vnd.wap.wml, application/vnd.wap.xhtml+xml, application/xhtml+xml, text/html, multipart/mixed, */*');
        $this->assertEquals(array('application/vnd.wap.wmlscriptc', 'text/vnd.wap.wml', 'application/vnd.wap.xhtml+xml', 'application/xhtml+xml', 'text/html', 'multipart/mixed', '*/*'), $request->getAcceptableContentTypes());
    }

    public function testGetLanguages()
    {
        $request = new Request();
        $this->assertEquals(array(), $request->getLanguages());

        $request = new Request();
        $request->headers->set('Accept-language', 'zh, en-us; q=0.8, en; q=0.6');
        $this->assertEquals(array('zh', 'en_US', 'en'), $request->getLanguages());
        $this->assertEquals(array('zh', 'en_US', 'en'), $request->getLanguages());

        $request = new Request();
        $request->headers->set('Accept-language', 'zh, en-us; q=0.6, en; q=0.8');
        $this->assertEquals(array('zh', 'en', 'en_US'), $request->getLanguages()); // Test out of order qvalues

        $request = new Request();
        $request->headers->set('Accept-language', 'zh, en, en-us');
        $this->assertEquals(array('zh', 'en', 'en_US'), $request->getLanguages()); // Test equal weighting without qvalues

        $request = new Request();
        $request->headers->set('Accept-language', 'zh; q=0.6, en, en-us; q=0.6');
        $this->assertEquals(array('en', 'zh', 'en_US'), $request->getLanguages()); // Test equal weighting with qvalues

        $request = new Request();
        $request->headers->set('Accept-language', 'zh, i-cherokee; q=0.6');
        $this->assertEquals(array('zh', 'cherokee'), $request->getLanguages());
    }

    public function testGetRequestFormat()
    {
        $request = new Request();
        $this->assertEquals('html', $request->getRequestFormat());

        $request = new Request();
        $this->assertNull($request->getRequestFormat(null));

        $request = new Request();
        $this->assertNull($request->setRequestFormat('foo'));
        $this->assertEquals('foo', $request->getRequestFormat(null));
    }

    public function testHasSession()
    {
        $request = new Request();

        $this->assertFalse($request->hasSession());
        $request->setSession(new Session(new MockArraySessionStorage()));
        $this->assertTrue($request->hasSession());
    }

    public function testGetSession()
    {
        $request = new Request();

        $request->setSession(new Session(new MockArraySessionStorage()));
        $this->assertTrue($request->hasSession());

        $session = $request->getSession();
        $this->assertObjectHasAttribute('storage', $session);
        $this->assertObjectHasAttribute('flashName', $session);
        $this->assertObjectHasAttribute('attributeName', $session);
    }

    public function testHasPreviousSession()
    {
        $request = new Request();

        $this->assertFalse($request->hasPreviousSession());
        $request->cookies->set('MOCKSESSID', 'foo');
        $this->assertFalse($request->hasPreviousSession());
        $request->setSession(new Session(new MockArraySessionStorage()));
        $this->assertTrue($request->hasPreviousSession());
    }

    public function testToString()
    {
        $request = new Request();

        $request->headers->set('Accept-language', 'zh, en-us; q=0.8, en; q=0.6');

        $this->assertContains('Accept-Language: zh, en-us; q=0.8, en; q=0.6', $request->__toString());
    }

    public function testIsMethod()
    {
        $request = new Request();
        $request->setMethod('POST');
        $this->assertTrue($request->isMethod('POST'));
        $this->assertTrue($request->isMethod('post'));
        $this->assertFalse($request->isMethod('GET'));
        $this->assertFalse($request->isMethod('get'));

        $request->setMethod('GET');
        $this->assertTrue($request->isMethod('GET'));
        $this->assertTrue($request->isMethod('get'));
        $this->assertFalse($request->isMethod('POST'));
        $this->assertFalse($request->isMethod('post'));
    }

    /**
     * @dataProvider getBaseUrlData
     */
    public function testGetBaseUrl($uri, $server, $expectedBaseUrl, $expectedPathInfo)
    {
        $request = Request::create($uri, 'GET', array(), array(), array(), $server);

        $this->assertSame($expectedBaseUrl, $request->getBaseUrl(), 'baseUrl');
        $this->assertSame($expectedPathInfo, $request->getPathInfo(), 'pathInfo');
    }

    public function getBaseUrlData()
    {
        return array(
            array(
                '/foo%20bar',
                array(
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo bar/app.php',
                    'SCRIPT_NAME'     => '/foo bar/app.php',
                    'PHP_SELF'        => '/foo bar/app.php',
                ),
                '/foo%20bar',
                '/',
            ),
            array(
                '/foo%20bar/home',
                array(
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo bar/app.php',
                    'SCRIPT_NAME'     => '/foo bar/app.php',
                    'PHP_SELF'        => '/foo bar/app.php',
                ),
                '/foo%20bar',
                '/home',
            ),
            array(
                '/foo%20bar/app.php/home',
                array(
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo bar/app.php',
                    'SCRIPT_NAME'     => '/foo bar/app.php',
                    'PHP_SELF'        => '/foo bar/app.php',
                ),
                '/foo%20bar/app.php',
                '/home',
            ),
            array(
                '/foo%20bar/app.php/home%3Dbaz',
                array(
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo bar/app.php',
                    'SCRIPT_NAME'     => '/foo bar/app.php',
                    'PHP_SELF'        => '/foo bar/app.php',
                ),
                '/foo%20bar/app.php',
                '/home%3Dbaz',
            ),
            array(
                '/foo/bar+baz',
                array(
                    'SCRIPT_FILENAME' => '/home/John Doe/public_html/foo/app.php',
                    'SCRIPT_NAME'     => '/foo/app.php',
                    'PHP_SELF'        => '/foo/app.php',
                ),
                '/foo',
                '/bar+baz',
            ),
        );
    }

    /**
     * @dataProvider urlencodedStringPrefixData
     */
    public function testUrlencodedStringPrefix($string, $prefix, $expect)
    {
        $request = new Request();

        $me = new \ReflectionMethod($request, 'getUrlencodedPrefix');
        $me->setAccessible(true);

        $this->assertSame($expect, $me->invoke($request, $string, $prefix));
    }

    public function urlencodedStringPrefixData()
    {
        return array(
            array('foo', 'foo', 'foo'),
            array('fo%6f', 'foo', 'fo%6f'),
            array('foo/bar', 'foo', 'foo'),
            array('fo%6f/bar', 'foo', 'fo%6f'),
            array('f%6f%6f/bar', 'foo', 'f%6f%6f'),
            array('%66%6F%6F/bar', 'foo', '%66%6F%6F'),
            array('fo+o/bar', 'fo+o', 'fo+o'),
            array('fo%2Bo/bar', 'fo+o', 'fo%2Bo'),
        );
    }

    private function disableHttpMethodParameterOverride()
    {
        $class = new \ReflectionClass('Symfony\\Component\\HttpFoundation\\Request');
        $property = $class->getProperty('httpMethodParameterOverride');
        $property->setAccessible(true);
        $property->setValue(false);
    }

    private function getRequestInstanceForClientIpTests($remoteAddr, $httpForwardedFor, $trustedProxies)
    {
        $request = new Request();

        $server = array('REMOTE_ADDR' => $remoteAddr);
        if (null !== $httpForwardedFor) {
            $server['HTTP_X_FORWARDED_FOR'] = $httpForwardedFor;
        }

        if ($trustedProxies) {
            Request::setTrustedProxies($trustedProxies);
        }

        $request->initialize(array(), array(), array(), array(), array(), $server);

        return $request;
    }

    public function testTrustedProxies()
    {
        $request = Request::create('http://example.com/');
        $request->server->set('REMOTE_ADDR', '3.3.3.3');
        $request->headers->set('X_FORWARDED_FOR', '1.1.1.1, 2.2.2.2');
        $request->headers->set('X_FORWARDED_HOST', 'foo.example.com, real.example.com:8080');
        $request->headers->set('X_FORWARDED_PROTO', 'https');
        $request->headers->set('X_FORWARDED_PORT', 443);
        $request->headers->set('X_MY_FOR', '3.3.3.3, 4.4.4.4');
        $request->headers->set('X_MY_HOST', 'my.example.com');
        $request->headers->set('X_MY_PROTO', 'http');
        $request->headers->set('X_MY_PORT', 81);

        // no trusted proxies
        $this->assertEquals('3.3.3.3', $request->getClientIp());
        $this->assertEquals('example.com', $request->getHost());
        $this->assertEquals(80, $request->getPort());
        $this->assertFalse($request->isSecure());

        // disabling proxy trusting
        Request::setTrustedProxies(array());
        $this->assertEquals('3.3.3.3', $request->getClientIp());
        $this->assertEquals('example.com', $request->getHost());
        $this->assertEquals(80, $request->getPort());
        $this->assertFalse($request->isSecure());

        // trusted proxy via setTrustedProxies()
        Request::setTrustedProxies(array('3.3.3.3', '2.2.2.2'));
        $this->assertEquals('1.1.1.1', $request->getClientIp());
        $this->assertEquals('real.example.com', $request->getHost());
        $this->assertEquals(443, $request->getPort());
        $this->assertTrue($request->isSecure());

        // check various X_FORWARDED_PROTO header values
        $request->headers->set('X_FORWARDED_PROTO', 'ssl');
        $this->assertTrue($request->isSecure());

        $request->headers->set('X_FORWARDED_PROTO', 'https, http');
        $this->assertTrue($request->isSecure());

        // custom header names
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_IP, 'X_MY_FOR');
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_HOST, 'X_MY_HOST');
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_PORT, 'X_MY_PORT');
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_PROTO, 'X_MY_PROTO');
        $this->assertEquals('4.4.4.4', $request->getClientIp());
        $this->assertEquals('my.example.com', $request->getHost());
        $this->assertEquals(81, $request->getPort());
        $this->assertFalse($request->isSecure());

        // disabling via empty header names
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_IP, null);
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_HOST, null);
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_PORT, null);
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_PROTO, null);
        $this->assertEquals('3.3.3.3', $request->getClientIp());
        $this->assertEquals('example.com', $request->getHost());
        $this->assertEquals(80, $request->getPort());
        $this->assertFalse($request->isSecure());

        // reset
        Request::setTrustedProxies(array());
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_IP, 'X_FORWARDED_FOR');
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_HOST, 'X_FORWARDED_HOST');
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_PORT, 'X_FORWARDED_PORT');
        Request::setTrustedHeaderName(Request::HEADER_CLIENT_PROTO, 'X_FORWARDED_PROTO');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetTrustedProxiesInvalidHeaderName()
    {
        Request::create('http://example.com/');
        Request::setTrustedHeaderName('bogus name', 'X_MY_FOR');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetTrustedProxiesInvalidHeaderName()
    {
        Request::create('http://example.com/');
        Request::getTrustedHeaderName('bogus name');
    }

    /**
     * @dataProvider iisRequestUriProvider
     */
    public function testIISRequestUri($headers, $server, $expectedRequestUri)
    {
        $request = new Request();
        $request->headers->replace($headers);
        $request->server->replace($server);

        $this->assertEquals($expectedRequestUri, $request->getRequestUri(), '->getRequestUri() is correct');

        $subRequestUri = '/bar/foo';
        $subRequest = Request::create($subRequestUri, 'get', array(), array(), array(), $request->server->all());
        $this->assertEquals($subRequestUri, $subRequest->getRequestUri(), '->getRequestUri() is correct in sub request');
    }

    public function iisRequestUriProvider()
    {
        return array(
            array(
                array(
                    'X_ORIGINAL_URL' => '/foo/bar',
                ),
                array(),
                '/foo/bar',
            ),
            array(
                array(
                    'X_REWRITE_URL' => '/foo/bar',
                ),
                array(),
                '/foo/bar',
            ),
            array(
                array(),
                array(
                    'IIS_WasUrlRewritten' => '1',
                    'UNENCODED_URL' => '/foo/bar',
                ),
                '/foo/bar',
            ),
            array(
                array(
                    'X_ORIGINAL_URL' => '/foo/bar',
                ),
                array(
                    'HTTP_X_ORIGINAL_URL' => '/foo/bar',
                ),
                '/foo/bar',
            ),
            array(
                array(
                    'X_ORIGINAL_URL' => '/foo/bar',
                ),
                array(
                    'IIS_WasUrlRewritten' => '1',
                    'UNENCODED_URL' => '/foo/bar',
                ),
                '/foo/bar',
            ),
            array(
                array(
                    'X_ORIGINAL_URL' => '/foo/bar',
                ),
                array(
                    'HTTP_X_ORIGINAL_URL' => '/foo/bar',
                    'IIS_WasUrlRewritten' => '1',
                    'UNENCODED_URL' => '/foo/bar',
                ),
                '/foo/bar',
            ),
            array(
                array(),
                array(
                    'ORIG_PATH_INFO' => '/foo/bar',
                ),
                '/foo/bar',
            ),
            array(
                array(),
                array(
                    'ORIG_PATH_INFO' => '/foo/bar',
                    'QUERY_STRING' => 'foo=bar',
                ),
                '/foo/bar?foo=bar',
            ),
        );
    }

    public function testTrustedHosts()
    {
        // create a request
        $request = Request::create('/');

        // no trusted host set -> no host check
        $request->headers->set('host', 'evil.com');
        $this->assertEquals('evil.com', $request->getHost());

        // add a trusted domain and all its subdomains
        Request::setTrustedHosts(array('.*\.?trusted.com$'));

        // untrusted host
        $request->headers->set('host', 'evil.com');
        try {
            $request->getHost();
            $this->fail('Request::getHost() should throw an exception when host is not trusted.');
        } catch (\UnexpectedValueException $e) {
            $this->assertEquals('Untrusted Host "evil.com"', $e->getMessage());
        }

        // trusted hosts
        $request->headers->set('host', 'trusted.com');
        $this->assertEquals('trusted.com', $request->getHost());
        $this->assertEquals(80, $request->getPort());

        $request->server->set('HTTPS', true);
        $request->headers->set('host', 'trusted.com');
        $this->assertEquals('trusted.com', $request->getHost());
        $this->assertEquals(443, $request->getPort());
        $request->server->set('HTTPS', false);

        $request->headers->set('host', 'trusted.com:8000');
        $this->assertEquals('trusted.com', $request->getHost());
        $this->assertEquals(8000, $request->getPort());

        $request->headers->set('host', 'subdomain.trusted.com');
        $this->assertEquals('subdomain.trusted.com', $request->getHost());

        // reset request for following tests
        Request::setTrustedHosts(array());
    }

    public function testFactory()
    {
        Request::setFactory(function (array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null) {
            return new NewRequest();
        });

        $this->assertEquals('foo', Request::create('/')->getFoo());

        Request::setFactory(null);
    }

    /**
     * @dataProvider getLongHostNames
     */
    public function testVeryLongHosts($host)
    {
        $start = microtime(true);

        $request = Request::create('/');
        $request->headers->set('host', $host);
        $this->assertEquals($host, $request->getHost());
        $this->assertLessThan(1, microtime(true) - $start);
    }

    /**
     * @dataProvider getHostValidities
     */
    public function testHostValidity($host, $isValid, $expectedHost = null, $expectedPort = null)
    {
        $request = Request::create('/');
        $request->headers->set('host', $host);

        if ($isValid) {
            $this->assertSame($expectedHost ?: $host, $request->getHost());
            if ($expectedPort) {
                $this->assertSame($expectedPort, $request->getPort());
            }
        } else {
            $this->setExpectedException('UnexpectedValueException', 'Invalid Host');
            $request->getHost();
        }
    }

    public function getHostValidities()
    {
        return array(
            array('.a', false),
            array('a..', false),
            array('a.', true),
            array("\xE9", false),
            array('[::1]', true),
            array('[::1]:80', true, '[::1]', 80),
            array(str_repeat('.', 101), false),
        );
    }

    public function getLongHostNames()
    {
        return array(
            array('a'.str_repeat('.a', 40000)),
            array(str_repeat(':', 101)),
        );
    }
}

class RequestContentProxy extends Request
{
    public function getContent($asResource = false)
    {
        return http_build_query(array('_method' => 'PUT', 'content' => 'mycontent'));
    }
}

class NewRequest extends Request
{
    public function getFoo()
    {
        return 'foo';
    }
}
