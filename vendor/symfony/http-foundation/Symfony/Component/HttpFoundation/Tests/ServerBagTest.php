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

use Symfony\Component\HttpFoundation\ServerBag;

/**
 * ServerBagTest
 *
 * @author Bulat Shakirzyanov <mallluhuct@gmail.com>
 */
class ServerBagTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldExtractHeadersFromServerArray()
    {
        $server = array(
            'SOME_SERVER_VARIABLE' => 'value',
            'SOME_SERVER_VARIABLE2' => 'value',
            'ROOT' => 'value',
            'HTTP_CONTENT_TYPE' => 'text/html',
            'HTTP_CONTENT_LENGTH' => '0',
            'HTTP_ETAG' => 'asdf',
            'PHP_AUTH_USER' => 'foo',
            'PHP_AUTH_PW' => 'bar',
        );

        $bag = new ServerBag($server);

        $this->assertEquals(array(
            'CONTENT_TYPE' => 'text/html',
            'CONTENT_LENGTH' => '0',
            'ETAG' => 'asdf',
            'AUTHORIZATION' => 'Basic '.base64_encode('foo:bar'),
            'PHP_AUTH_USER' => 'foo',
            'PHP_AUTH_PW' => 'bar',
        ), $bag->getHeaders());
    }

    public function testHttpPasswordIsOptional()
    {
        $bag = new ServerBag(array('PHP_AUTH_USER' => 'foo'));

        $this->assertEquals(array(
            'AUTHORIZATION' => 'Basic '.base64_encode('foo:'),
            'PHP_AUTH_USER' => 'foo',
            'PHP_AUTH_PW' => '',
        ), $bag->getHeaders());
    }

    public function testHttpBasicAuthWithPhpCgi()
    {
        $bag = new ServerBag(array('HTTP_AUTHORIZATION' => 'Basic '.base64_encode('foo:bar')));

        $this->assertEquals(array(
            'AUTHORIZATION' => 'Basic '.base64_encode('foo:bar'),
            'PHP_AUTH_USER' => 'foo',
            'PHP_AUTH_PW' => 'bar',
        ), $bag->getHeaders());
    }

    public function testHttpBasicAuthWithPhpCgiBogus()
    {
        $bag = new ServerBag(array('HTTP_AUTHORIZATION' => 'Basic_'.base64_encode('foo:bar')));

        // Username and passwords should not be set as the header is bogus
        $headers = $bag->getHeaders();
        $this->assertFalse(isset($headers['PHP_AUTH_USER']));
        $this->assertFalse(isset($headers['PHP_AUTH_PW']));
    }

    public function testHttpBasicAuthWithPhpCgiRedirect()
    {
        $bag = new ServerBag(array('REDIRECT_HTTP_AUTHORIZATION' => 'Basic '.base64_encode('username:pass:word')));

        $this->assertEquals(array(
            'AUTHORIZATION' => 'Basic '.base64_encode('username:pass:word'),
            'PHP_AUTH_USER' => 'username',
            'PHP_AUTH_PW' => 'pass:word',
        ), $bag->getHeaders());
    }

    public function testHttpBasicAuthWithPhpCgiEmptyPassword()
    {
        $bag = new ServerBag(array('HTTP_AUTHORIZATION' => 'Basic '.base64_encode('foo:')));

        $this->assertEquals(array(
            'AUTHORIZATION' => 'Basic '.base64_encode('foo:'),
            'PHP_AUTH_USER' => 'foo',
            'PHP_AUTH_PW' => '',
        ), $bag->getHeaders());
    }

    public function testHttpDigestAuthWithPhpCgi()
    {
        $digest = 'Digest username="foo", realm="acme", nonce="'.md5('secret').'", uri="/protected, qop="auth"';
        $bag = new ServerBag(array('HTTP_AUTHORIZATION' => $digest));

        $this->assertEquals(array(
            'AUTHORIZATION' => $digest,
            'PHP_AUTH_DIGEST' => $digest,
        ), $bag->getHeaders());
    }

    public function testHttpDigestAuthWithPhpCgiBogus()
    {
        $digest = 'Digest_username="foo", realm="acme", nonce="'.md5('secret').'", uri="/protected, qop="auth"';
        $bag = new ServerBag(array('HTTP_AUTHORIZATION' => $digest));

        // Username and passwords should not be set as the header is bogus
        $headers = $bag->getHeaders();
        $this->assertFalse(isset($headers['PHP_AUTH_USER']));
        $this->assertFalse(isset($headers['PHP_AUTH_PW']));
    }

    public function testHttpDigestAuthWithPhpCgiRedirect()
    {
        $digest = 'Digest username="foo", realm="acme", nonce="'.md5('secret').'", uri="/protected, qop="auth"';
        $bag = new ServerBag(array('REDIRECT_HTTP_AUTHORIZATION' => $digest));

        $this->assertEquals(array(
            'AUTHORIZATION' => $digest,
            'PHP_AUTH_DIGEST' => $digest,
        ), $bag->getHeaders());
    }

    public function testOAuthBearerAuth()
    {
        $headerContent = 'Bearer L-yLEOr9zhmUYRkzN1jwwxwQ-PBNiKDc8dgfB4hTfvo';
        $bag = new ServerBag(array('HTTP_AUTHORIZATION' => $headerContent));

        $this->assertEquals(array(
            'AUTHORIZATION' => $headerContent,
        ), $bag->getHeaders());
    }
}
