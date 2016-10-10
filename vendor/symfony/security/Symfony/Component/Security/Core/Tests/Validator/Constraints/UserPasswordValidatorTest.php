<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Tests\Validator\Constraints;

use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Security\Core\Validator\Constraints\UserPasswordValidator;

class UserPasswordValidatorTest extends \PHPUnit_Framework_TestCase
{
    const PASSWORD_VALID   = true;
    const PASSWORD_INVALID = false;

    protected $context;

    protected function setUp()
    {
        $this->context = $this->getMock('Symfony\Component\Validator\ExecutionContext', array(), array(), '', false);
    }

    protected function tearDown()
    {
        $this->context = null;
    }

    public function testPasswordIsValid()
    {
        $user = $this->createUser();
        $securityContext = $this->createSecurityContext($user);

        $encoder = $this->createPasswordEncoder(static::PASSWORD_VALID);
        $encoderFactory = $this->createEncoderFactory($encoder);

        $validator = new UserPasswordValidator($securityContext, $encoderFactory);
        $validator->initialize($this->context);

        $this
            ->context
            ->expects($this->never())
            ->method('addViolation')
        ;

        $validator->validate('secret', new UserPassword());
    }

    public function testPasswordIsNotValid()
    {
        $user = $this->createUser();
        $securityContext = $this->createSecurityContext($user);

        $encoder = $this->createPasswordEncoder(static::PASSWORD_INVALID);
        $encoderFactory = $this->createEncoderFactory($encoder);

        $validator = new UserPasswordValidator($securityContext, $encoderFactory);
        $validator->initialize($this->context);

        $this
            ->context
            ->expects($this->once())
            ->method('addViolation')
        ;

        $validator->validate('secret', new UserPassword());
    }

    public function testUserIsNotValid()
    {
        $this->setExpectedException('Symfony\Component\Validator\Exception\ConstraintDefinitionException');

        $user = $this->getMock('Foo\Bar\User');
        $encoderFactory = $this->getMock('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface');
        $securityContext = $this->createSecurityContext($user);

        $validator = new UserPasswordValidator($securityContext, $encoderFactory);
        $validator->initialize($this->context);
        $validator->validate('secret', new UserPassword());
    }

    protected function createUser()
    {
        $mock = $this->getMock('Symfony\Component\Security\Core\User\UserInterface');

        $mock
            ->expects($this->once())
            ->method('getPassword')
            ->will($this->returnValue('s3Cr3t'))
        ;

        $mock
            ->expects($this->once())
            ->method('getSalt')
            ->will($this->returnValue('^S4lt$'))
        ;

        return $mock;
    }

    protected function createPasswordEncoder($isPasswordValid = true)
    {
        $mock = $this->getMock('Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface');

        $mock
            ->expects($this->once())
            ->method('isPasswordValid')
            ->will($this->returnValue($isPasswordValid))
        ;

        return $mock;
    }

    protected function createEncoderFactory($encoder = null)
    {
        $mock = $this->getMock('Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface');

        $mock
            ->expects($this->once())
            ->method('getEncoder')
            ->will($this->returnValue($encoder))
        ;

        return $mock;
    }

    protected function createSecurityContext($user = null)
    {
        $token = $this->createAuthenticationToken($user);

        $mock = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $mock
            ->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($token))
        ;

        return $mock;
    }

    protected function createAuthenticationToken($user = null)
    {
        $mock = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $mock
            ->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($user))
        ;

        return $mock;
    }
}
