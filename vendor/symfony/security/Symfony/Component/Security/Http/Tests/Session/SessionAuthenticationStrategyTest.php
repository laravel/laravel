<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\Tests\Session;

use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategy;

class SessionAuthenticationStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testSessionIsNotChanged()
    {
        $request = $this->getRequest();
        $request->expects($this->never())->method('getSession');

        $strategy = new SessionAuthenticationStrategy(SessionAuthenticationStrategy::NONE);
        $strategy->onAuthentication($request, $this->getToken());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid session authentication strategy "foo"
     */
    public function testUnsupportedStrategy()
    {
        $request = $this->getRequest();
        $request->expects($this->never())->method('getSession');

        $strategy = new SessionAuthenticationStrategy('foo');
        $strategy->onAuthentication($request, $this->getToken());
    }

    public function testSessionIsMigrated()
    {
        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\SessionInterface');
        $session->expects($this->once())->method('migrate');

        $strategy = new SessionAuthenticationStrategy(SessionAuthenticationStrategy::MIGRATE);
        $strategy->onAuthentication($this->getRequest($session), $this->getToken());
    }

    public function testSessionIsInvalidated()
    {
        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\SessionInterface');
        $session->expects($this->once())->method('invalidate');

        $strategy = new SessionAuthenticationStrategy(SessionAuthenticationStrategy::INVALIDATE);
        $strategy->onAuthentication($this->getRequest($session), $this->getToken());
    }

    private function getRequest($session = null)
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');

        if (null !== $session) {
            $request->expects($this->any())->method('getSession')->will($this->returnValue($session));
        }

        return $request;
    }

    private function getToken()
    {
        return $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
    }
}
