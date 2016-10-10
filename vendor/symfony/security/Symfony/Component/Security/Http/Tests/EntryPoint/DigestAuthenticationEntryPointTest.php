<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\Tests\EntryPoint;

use Symfony\Component\Security\Http\EntryPoint\DigestAuthenticationEntryPoint;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;

class DigestAuthenticationEntryPointTest extends \PHPUnit_Framework_TestCase
{
    public function testStart()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');

        $authenticationException = new AuthenticationException('TheAuthenticationExceptionMessage');

        $entryPoint = new DigestAuthenticationEntryPoint('TheRealmName', 'TheKey');
        $response = $entryPoint->start($request, $authenticationException);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertRegExp('/^Digest realm="TheRealmName", qop="auth", nonce="[a-zA-Z0-9\/+]+={0,2}"$/', $response->headers->get('WWW-Authenticate'));
    }

    public function testStartWithNoException()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');

        $entryPoint = new DigestAuthenticationEntryPoint('TheRealmName', 'TheKey');
        $response = $entryPoint->start($request);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertRegExp('/^Digest realm="TheRealmName", qop="auth", nonce="[a-zA-Z0-9\/+]+={0,2}"$/', $response->headers->get('WWW-Authenticate'));
    }

    public function testStartWithNonceExpiredException()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');

        $nonceExpiredException = new NonceExpiredException('TheNonceExpiredExceptionMessage');

        $entryPoint = new DigestAuthenticationEntryPoint('TheRealmName', 'TheKey');
        $response = $entryPoint->start($request, $nonceExpiredException);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertRegExp('/^Digest realm="TheRealmName", qop="auth", nonce="[a-zA-Z0-9\/+]+={0,2}", stale="true"$/', $response->headers->get('WWW-Authenticate'));
    }
}
