<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\Tests\Firewall;

use Symfony\Component\Security\Http\Firewall\SwitchUserListener;

class SwitchUserListenerTest extends \PHPUnit_Framework_TestCase
{
    private $securityContext;

    private $userProvider;

    private $userChecker;

    private $accessDecisionManager;

    private $request;

    private $event;

    protected function setUp()
    {
        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $this->userProvider = $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $this->userChecker = $this->getMock('Symfony\Component\Security\Core\User\UserCheckerInterface');
        $this->accessDecisionManager = $this->getMock('Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface');
        $this->request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $this->request->query = $this->getMock('Symfony\Component\HttpFoundation\ParameterBag');
        $this->request->server = $this->getMock('Symfony\Component\HttpFoundation\ServerBag');
        $this->event = $this->getEvent($this->request);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $providerKey must not be empty
     */
    public function testProviderKeyIsRequired()
    {
        new SwitchUserListener($this->securityContext, $this->userProvider, $this->userChecker, '', $this->accessDecisionManager);
    }

    public function testEventIsIgnoredIfUsernameIsNotPassedWithTheRequest()
    {
        $this->request->expects($this->any())->method('get')->with('_switch_user')->will($this->returnValue(null));

        $this->event->expects($this->never())->method('setResponse');
        $this->securityContext->expects($this->never())->method('setToken');

        $listener = new SwitchUserListener($this->securityContext, $this->userProvider, $this->userChecker, 'provider123', $this->accessDecisionManager);
        $listener->handle($this->event);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException
     */
    public function testExitUserThrowsAuthenticationExceptionIfOriginalTokenCannotBeFound()
    {
        $token = $this->getToken(array($this->getMock('Symfony\Component\Security\Core\Role\RoleInterface')));

        $this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue($token));
        $this->request->expects($this->any())->method('get')->with('_switch_user')->will($this->returnValue('_exit'));

        $listener = new SwitchUserListener($this->securityContext, $this->userProvider, $this->userChecker, 'provider123', $this->accessDecisionManager);
        $listener->handle($this->event);
    }

    public function testExitUserUpdatesToken()
    {
        $originalToken = $this->getToken();
        $role = $this->getMockBuilder('Symfony\Component\Security\Core\Role\SwitchUserRole')
            ->disableOriginalConstructor()
            ->getMock();
        $role->expects($this->any())->method('getSource')->will($this->returnValue($originalToken));

        $this->securityContext->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($this->getToken(array($role))));

        $this->request->expects($this->any())->method('get')->with('_switch_user')->will($this->returnValue('_exit'));
        $this->request->expects($this->any())->method('getUri')->will($this->returnValue('/'));
        $this->request->query->expects($this->once())->method('remove','_switch_user');
        $this->request->query->expects($this->any())->method('all')->will($this->returnValue(array()));
        $this->request->server->expects($this->once())->method('set')->with('QUERY_STRING', '');

        $this->securityContext->expects($this->once())
            ->method('setToken')->with($originalToken);
        $this->event->expects($this->once())
            ->method('setResponse')->with($this->isInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse'));

        $listener = new SwitchUserListener($this->securityContext, $this->userProvider, $this->userChecker, 'provider123', $this->accessDecisionManager);
        $listener->handle($this->event);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testSwitchUserIsDissallowed()
    {
        $token = $this->getToken(array($this->getMock('Symfony\Component\Security\Core\Role\RoleInterface')));

        $this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue($token));
        $this->request->expects($this->any())->method('get')->with('_switch_user')->will($this->returnValue('kuba'));

        $this->accessDecisionManager->expects($this->once())
            ->method('decide')->with($token, array('ROLE_ALLOWED_TO_SWITCH'))
            ->will($this->returnValue(false));

        $listener = new SwitchUserListener($this->securityContext, $this->userProvider, $this->userChecker, 'provider123', $this->accessDecisionManager);
        $listener->handle($this->event);
    }

    public function testSwitchUser()
    {
        $token = $this->getToken(array($this->getMock('Symfony\Component\Security\Core\Role\RoleInterface')));
        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $user->expects($this->any())->method('getRoles')->will($this->returnValue(array()));

        $this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue($token));
        $this->request->expects($this->any())->method('get')->with('_switch_user')->will($this->returnValue('kuba'));
        $this->request->query->expects($this->once())->method('remove','_switch_user');
        $this->request->query->expects($this->any())->method('all')->will($this->returnValue(array()));

        $this->request->expects($this->any())->method('getUri')->will($this->returnValue('/'));
        $this->request->server->expects($this->once())->method('set')->with('QUERY_STRING', '');

        $this->accessDecisionManager->expects($this->once())
            ->method('decide')->with($token, array('ROLE_ALLOWED_TO_SWITCH'))
            ->will($this->returnValue(true));

        $this->userProvider->expects($this->once())
            ->method('loadUserByUsername')->with('kuba')
            ->will($this->returnValue($user));
        $this->userChecker->expects($this->once())
            ->method('checkPostAuth')->with($user);
        $this->securityContext->expects($this->once())
            ->method('setToken')->with($this->isInstanceOf('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken'));

        $listener = new SwitchUserListener($this->securityContext, $this->userProvider, $this->userChecker, 'provider123', $this->accessDecisionManager);
        $listener->handle($this->event);
    }

    public function testSwitchUserKeepsOtherQueryStringParameters()
    {
        $token = $this->getToken(array($this->getMock('Symfony\Component\Security\Core\Role\RoleInterface')));
        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $user->expects($this->any())->method('getRoles')->will($this->returnValue(array()));

        $this->securityContext->expects($this->any())->method('getToken')->will($this->returnValue($token));
        $this->request->expects($this->any())->method('get')->with('_switch_user')->will($this->returnValue('kuba'));
        $this->request->query->expects($this->once())->method('remove','_switch_user');
        $this->request->query->expects($this->any())->method('all')->will($this->returnValue(array('page'=>3,'section'=>2)));
        $this->request->expects($this->any())->method('getUri')->will($this->returnValue('/'));
        $this->request->server->expects($this->once())->method('set')->with('QUERY_STRING', 'page=3&section=2');

        $this->accessDecisionManager->expects($this->once())
            ->method('decide')->with($token, array('ROLE_ALLOWED_TO_SWITCH'))
            ->will($this->returnValue(true));

        $this->userProvider->expects($this->once())
            ->method('loadUserByUsername')->with('kuba')
            ->will($this->returnValue($user));
        $this->userChecker->expects($this->once())
            ->method('checkPostAuth')->with($user);
        $this->securityContext->expects($this->once())
            ->method('setToken')->with($this->isInstanceOf('Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken'));

        $listener = new SwitchUserListener($this->securityContext, $this->userProvider, $this->userChecker, 'provider123', $this->accessDecisionManager);
        $listener->handle($this->event);
    }

    private function getEvent($request)
    {
        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($request));

        return $event;
    }

    private function getToken(array $roles = array())
    {
        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->any())
            ->method('getRoles')
            ->will($this->returnValue($roles));

        return $token;
    }
}
