<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Tests\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\SwitchUserRole;

class TestUser
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function __toString()
    {
        return $this->name;
    }
}

class ConcreteToken extends AbstractToken
{
    private $credentials = 'credentials_value';

    public function __construct($user, array $roles = array())
    {
        parent::__construct($roles);

        $this->setUser($user);
    }

    public function serialize()
    {
        return serialize(array($this->credentials, parent::serialize()));
    }

    public function unserialize($serialized)
    {
        list($this->credentials, $parentStr) = unserialize($serialized);
        parent::unserialize($parentStr);
    }

    public function getCredentials() {}
}

class AbstractTokenTest extends \PHPUnit_Framework_TestCase
{
    public function testGetUsername()
    {
        $token = $this->getToken(array('ROLE_FOO'));
        $token->setUser('fabien');
        $this->assertEquals('fabien', $token->getUsername());

        $token->setUser(new TestUser('fabien'));
        $this->assertEquals('fabien', $token->getUsername());

        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $user->expects($this->once())->method('getUsername')->will($this->returnValue('fabien'));
        $token->setUser($user);
        $this->assertEquals('fabien', $token->getUsername());
    }

    public function testEraseCredentials()
    {
        $token = $this->getToken(array('ROLE_FOO'));

        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $user->expects($this->once())->method('eraseCredentials');
        $token->setUser($user);

        $token->eraseCredentials();
    }

    /**
     * @covers Symfony\Component\Security\Core\Authentication\Token\AbstractToken::serialize
     * @covers Symfony\Component\Security\Core\Authentication\Token\AbstractToken::unserialize
     */
    public function testSerialize()
    {
        $token = $this->getToken(array('ROLE_FOO'));
        $token->setAttributes(array('foo' => 'bar'));

        $uToken = unserialize(serialize($token));

        $this->assertEquals($token->getRoles(), $uToken->getRoles());
        $this->assertEquals($token->getAttributes(), $uToken->getAttributes());
    }

    public function testSerializeParent()
    {
        $user = new TestUser('fabien');
        $token = new ConcreteToken($user, array('ROLE_FOO'));

        $parentToken = new ConcreteToken($user, array(new SwitchUserRole('ROLE_PREVIOUS', $token)));
        $uToken = unserialize(serialize($parentToken));

        $this->assertEquals(
            current($parentToken->getRoles())->getSource()->getUser(),
            current($uToken->getRoles())->getSource()->getUser()
        );
    }

    /**
     * @covers Symfony\Component\Security\Core\Authentication\Token\AbstractToken::__construct
     */
    public function testConstructor()
    {
        $token = $this->getToken(array('ROLE_FOO'));
        $this->assertEquals(array(new Role('ROLE_FOO')), $token->getRoles());

        $token = $this->getToken(array(new Role('ROLE_FOO')));
        $this->assertEquals(array(new Role('ROLE_FOO')), $token->getRoles());

        $token = $this->getToken(array(new Role('ROLE_FOO'), 'ROLE_BAR'));
        $this->assertEquals(array(new Role('ROLE_FOO'), new Role('ROLE_BAR')), $token->getRoles());
    }

    /**
     * @covers Symfony\Component\Security\Core\Authentication\Token\AbstractToken::isAuthenticated
     * @covers Symfony\Component\Security\Core\Authentication\Token\AbstractToken::setAuthenticated
     */
    public function testAuthenticatedFlag()
    {
        $token = $this->getToken();
        $this->assertFalse($token->isAuthenticated());

        $token->setAuthenticated(true);
        $this->assertTrue($token->isAuthenticated());

        $token->setAuthenticated(false);
        $this->assertFalse($token->isAuthenticated());
    }

    /**
     * @covers Symfony\Component\Security\Core\Authentication\Token\AbstractToken::getAttributes
     * @covers Symfony\Component\Security\Core\Authentication\Token\AbstractToken::setAttributes
     * @covers Symfony\Component\Security\Core\Authentication\Token\AbstractToken::hasAttribute
     * @covers Symfony\Component\Security\Core\Authentication\Token\AbstractToken::getAttribute
     * @covers Symfony\Component\Security\Core\Authentication\Token\AbstractToken::setAttribute
     */
    public function testAttributes()
    {
        $attributes = array('foo' => 'bar');
        $token = $this->getToken();
        $token->setAttributes($attributes);

        $this->assertEquals($attributes, $token->getAttributes(), '->getAttributes() returns the token attributes');
        $this->assertEquals('bar', $token->getAttribute('foo'), '->getAttribute() returns the value of an attribute');
        $token->setAttribute('foo', 'foo');
        $this->assertEquals('foo', $token->getAttribute('foo'), '->setAttribute() changes the value of an attribute');
        $this->assertTrue($token->hasAttribute('foo'), '->hasAttribute() returns true if the attribute is defined');
        $this->assertFalse($token->hasAttribute('oof'), '->hasAttribute() returns false if the attribute is not defined');

        try {
            $token->getAttribute('foobar');
            $this->fail('->getAttribute() throws an \InvalidArgumentException exception when the attribute does not exist');
        } catch (\Exception $e) {
            $this->assertInstanceOf('\InvalidArgumentException', $e, '->getAttribute() throws an \InvalidArgumentException exception when the attribute does not exist');
            $this->assertEquals('This token has no "foobar" attribute.', $e->getMessage(), '->getAttribute() throws an \InvalidArgumentException exception when the attribute does not exist');
        }
    }

    /**
     * @dataProvider getUsers
     */
    public function testSetUser($user)
    {
        $token = $this->getToken();
        $token->setUser($user);
        $this->assertSame($user, $token->getUser());
    }

    public function getUsers()
    {
        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $advancedUser = $this->getMock('Symfony\Component\Security\Core\User\AdvancedUserInterface');

        return array(
            array($advancedUser),
            array($user),
            array(new TestUser('foo')),
            array('foo'),
        );
    }

    /**
     * @dataProvider getUserChanges
     */
    public function testSetUserSetsAuthenticatedToFalseWhenUserChanges($firstUser, $secondUser)
    {
        $token = $this->getToken();
        $token->setAuthenticated(true);
        $this->assertTrue($token->isAuthenticated());

        $token->setUser($firstUser);
        $this->assertTrue($token->isAuthenticated());

        $token->setUser($secondUser);
        $this->assertFalse($token->isAuthenticated());
    }

    public function getUserChanges()
    {
        $user = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
        $advancedUser = $this->getMock('Symfony\Component\Security\Core\User\AdvancedUserInterface');

        return array(
            array(
                'foo', 'bar',
            ),
            array(
                'foo', new TestUser('bar'),
            ),
            array(
                'foo', $user,
            ),
            array(
                'foo', $advancedUser
            ),
            array(
                $user, 'foo'
            ),
            array(
                $advancedUser, 'foo'
            ),
            array(
                $user, new TestUser('foo'),
            ),
            array(
                $advancedUser, new TestUser('foo'),
            ),
            array(
                new TestUser('foo'), new TestUser('bar'),
            ),
            array(
                new TestUser('foo'), 'bar',
            ),
            array(
                new TestUser('foo'), $user,
            ),
            array(
                new TestUser('foo'), $advancedUser,
            ),
            array(
                $user, $advancedUser
            ),
            array(
                $advancedUser, $user
            ),
        );
    }

    /**
     * @dataProvider getUsers
     */
    public function testSetUserDoesNotSetAuthenticatedToFalseWhenUserDoesNotChange($user)
    {
        $token = $this->getToken();
        $token->setAuthenticated(true);
        $this->assertTrue($token->isAuthenticated());

        $token->setUser($user);
        $this->assertTrue($token->isAuthenticated());

        $token->setUser($user);
        $this->assertTrue($token->isAuthenticated());
    }

    protected function getToken(array $roles = array())
    {
        return $this->getMockForAbstractClass('Symfony\Component\Security\Core\Authentication\Token\AbstractToken', array($roles));
    }
}
