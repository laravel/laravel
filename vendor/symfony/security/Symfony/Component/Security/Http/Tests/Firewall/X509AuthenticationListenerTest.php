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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Firewall\X509AuthenticationListener;

class X509AuthenticationListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataProviderGetPreAuthenticatedData
     */
    public function testGetPreAuthenticatedData($user, $credentials)
    {
        $serverVars = array();
        if ('' !== $user) {
            $serverVars['SSL_CLIENT_S_DN_Email'] = $user;
        }
        if ('' !== $credentials) {
            $serverVars['SSL_CLIENT_S_DN'] = $credentials;
        }

        $request = new Request(array(), array(), array(), array(), array(), $serverVars);

        $context = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');

        $authenticationManager = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface');

        $listener = new X509AuthenticationListener($context, $authenticationManager, 'TheProviderKey');

        $method = new \ReflectionMethod($listener, 'getPreAuthenticatedData');
        $method->setAccessible(true);

        $result = $method->invokeArgs($listener, array($request));
        $this->assertSame($result, array($user, $credentials));
    }

    public static function dataProviderGetPreAuthenticatedData()
    {
        return array(
            'validValues' => array('TheUser', 'TheCredentials'),
            'noCredentials' => array('TheUser', ''),
        );
    }

    /**
     * @dataProvider dataProviderGetPreAuthenticatedDataNoUser
     */
    public function testGetPreAuthenticatedDataNoUser($emailAddress)
    {
        $credentials = 'CN=Sample certificate DN/emailAddress='.$emailAddress;
        $request = new Request(array(), array(), array(), array(), array(), array('SSL_CLIENT_S_DN' => $credentials));

        $context = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');

        $authenticationManager = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface');

        $listener = new X509AuthenticationListener($context, $authenticationManager, 'TheProviderKey');

        $method = new \ReflectionMethod($listener, 'getPreAuthenticatedData');
        $method->setAccessible(true);

        $result = $method->invokeArgs($listener, array($request));
        $this->assertSame($result, array($emailAddress, $credentials));
    }

    public static function dataProviderGetPreAuthenticatedDataNoUser()
    {
        return array(
            'basicEmailAddress' => array('cert@example.com'),
            'emailAddressWithPlusSign' => array('cert+something@example.com'),
        );
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\BadCredentialsException
     */
    public function testGetPreAuthenticatedDataNoData()
    {
        $request = new Request(array(), array(), array(), array(), array(), array());

        $context = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');

        $authenticationManager = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface');

        $listener = new X509AuthenticationListener($context, $authenticationManager, 'TheProviderKey');

        $method = new \ReflectionMethod($listener, 'getPreAuthenticatedData');
        $method->setAccessible(true);

        $result = $method->invokeArgs($listener, array($request));
    }

    public function testGetPreAuthenticatedDataWithDifferentKeys()
    {
        $userCredentials = array('TheUser', 'TheCredentials');

        $request = new Request(array(), array(), array(), array(), array(), array(
            'TheUserKey' => 'TheUser',
            'TheCredentialsKey' => 'TheCredentials'
        ));
        $context = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');

        $authenticationManager = $this->getMock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface');

        $listener = new X509AuthenticationListener($context, $authenticationManager, 'TheProviderKey', 'TheUserKey', 'TheCredentialsKey');

        $method = new \ReflectionMethod($listener, 'getPreAuthenticatedData');
        $method->setAccessible(true);

        $result = $method->invokeArgs($listener, array($request));
        $this->assertSame($result, $userCredentials);
    }
}
