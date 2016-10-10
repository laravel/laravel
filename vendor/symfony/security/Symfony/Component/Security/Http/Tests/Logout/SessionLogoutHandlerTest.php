<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\Tests\Logout;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Logout\SessionLogoutHandler;

class SessionLogoutHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testLogout()
    {
        $handler = new SessionLogoutHandler();

        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $response = new Response();
        $session = $this->getMock('Symfony\Component\HttpFoundation\Session\Session', array(), array(), '', false);

        $request
            ->expects($this->once())
            ->method('getSession')
            ->will($this->returnValue($session))
        ;

        $session
            ->expects($this->once())
            ->method('invalidate')
        ;

        $handler->logout($request, $response, $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface'));
    }
}
