<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Tests;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;

class RequestContextTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $requestContext = new RequestContext(
            'foo',
            'post',
            'foo.bar',
            'HTTPS',
            8080,
            444,
            '/baz',
            'bar=foobar'
        );

        $this->assertEquals('foo', $requestContext->getBaseUrl());
        $this->assertEquals('POST', $requestContext->getMethod());
        $this->assertEquals('foo.bar', $requestContext->getHost());
        $this->assertEquals('https', $requestContext->getScheme());
        $this->assertSame(8080, $requestContext->getHttpPort());
        $this->assertSame(444, $requestContext->getHttpsPort());
        $this->assertEquals('/baz', $requestContext->getPathInfo());
        $this->assertEquals('bar=foobar', $requestContext->getQueryString());
    }

    public function testFromRequest()
    {
        $request = Request::create('https://test.com:444/foo?bar=baz');
        $requestContext = new RequestContext();
        $requestContext->setHttpPort(123);
        $requestContext->fromRequest($request);

        $this->assertEquals('', $requestContext->getBaseUrl());
        $this->assertEquals('GET', $requestContext->getMethod());
        $this->assertEquals('test.com', $requestContext->getHost());
        $this->assertEquals('https', $requestContext->getScheme());
        $this->assertEquals('/foo', $requestContext->getPathInfo());
        $this->assertEquals('bar=baz', $requestContext->getQueryString());
        $this->assertSame(123, $requestContext->getHttpPort());
        $this->assertSame(444, $requestContext->getHttpsPort());

        $request = Request::create('http://test.com:8080/foo?bar=baz');
        $requestContext = new RequestContext();
        $requestContext->setHttpsPort(567);
        $requestContext->fromRequest($request);

        $this->assertSame(8080, $requestContext->getHttpPort());
        $this->assertSame(567, $requestContext->getHttpsPort());
    }

    public function testGetParameters()
    {
        $requestContext = new RequestContext();
        $this->assertEquals(array(), $requestContext->getParameters());

        $requestContext->setParameters(array('foo' => 'bar'));
        $this->assertEquals(array('foo' => 'bar'), $requestContext->getParameters());
    }

    public function testHasParameter()
    {
        $requestContext = new RequestContext();
        $requestContext->setParameters(array('foo' => 'bar'));

        $this->assertTrue($requestContext->hasParameter('foo'));
        $this->assertFalse($requestContext->hasParameter('baz'));
    }

    public function testGetParameter()
    {
        $requestContext = new RequestContext();
        $requestContext->setParameters(array('foo' => 'bar'));

        $this->assertEquals('bar', $requestContext->getParameter('foo'));
        $this->assertNull($requestContext->getParameter('baz'));
    }

    public function testSetParameter()
    {
        $requestContext = new RequestContext();
        $requestContext->setParameter('foo', 'bar');

        $this->assertEquals('bar', $requestContext->getParameter('foo'));
    }
}
