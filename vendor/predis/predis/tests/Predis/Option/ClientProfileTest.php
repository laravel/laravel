<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Option;

use PredisTestCase;
use Predis\Command\Processor\KeyPrefixProcessor;
use Predis\Profile\ServerProfile;

/**
 *
 */
class ClientProfileTest extends PredisTestCase
{
    /**
     * @group disconnected
     */
    public function testValidationReturnsServerProfileWithStringValue()
    {
        $options = $this->getMock('Predis\Option\ClientOptionsInterface');
        $option = new ClientProfile();

        $profile = $option->filter($options, '2.0');

        $this->assertInstanceOf('Predis\Profile\ServerProfileInterface', $profile);
        $this->assertEquals('2.0', $profile->getVersion());
        $this->assertNull($profile->getProcessor());
    }

    /**
     * @group disconnected
     */
    public function testValidationAcceptsProfileInstancesAsValue()
    {
        $value = ServerProfile::get('2.0');
        $options = $this->getMock('Predis\Option\ClientOptionsInterface');
        $option = new ClientProfile();

        $profile = $option->filter($options, $value);

        $this->assertInstanceOf('Predis\Profile\ServerProfileInterface', $profile);
        $this->assertEquals('2.0', $profile->getVersion());
        $this->assertNull($profile->getProcessor());
    }

    /**
     * @group disconnected
     */
    public function testValidationAcceptsCallableObjectAsInitializers()
    {
        $value = $this->getMock('Predis\Profile\ServerProfileInterface');

        $options = $this->getMock('Predis\Option\ClientOptionsInterface');
        $option = new ClientProfile();

        $initializer = $this->getMock('stdClass', array('__invoke'));
        $initializer->expects($this->once())
                    ->method('__invoke')
                    ->with($this->isInstanceOf('Predis\Option\ClientOptionsInterface'), $option)
                    ->will($this->returnValue($value));

        $profile = $option->filter($options, $initializer, $option);

        $this->assertInstanceOf('Predis\Profile\ServerProfileInterface', $profile);
        $this->assertSame($value, $profile);
    }

    /**
     * @group disconnected
     */
    public function testValidationThrowsExceptionOnWrongInvalidArguments()
    {
        $this->setExpectedException('InvalidArgumentException');

        $options = $this->getMock('Predis\Option\ClientOptionsInterface');
        $option = new ClientProfile();

        $option->filter($options, new \stdClass());
    }

    /**
     * @group disconnected
     */
    public function testDefaultReturnsDefaultServerProfile()
    {
        $options = $this->getMock('Predis\Option\ClientOptionsInterface');
        $option = new ClientProfile();

        $profile = $option->getDefault($options);

        $this->assertInstanceOf('Predis\Profile\ServerProfileInterface', $profile);
        $this->assertInstanceOf(get_class(ServerProfile::getDefault()), $profile);
        $this->assertNull($profile->getProcessor());
    }

    /**
     * @group disconnected
     */
    public function testInvokeReturnsSpecifiedServerProfileOrDefault()
    {
        $options = $this->getMock('Predis\Option\ClientOptionsInterface');
        $option = new ClientProfile();

        $profile = $option($options, '2.0');

        $this->assertInstanceOf('Predis\Profile\ServerProfileInterface', $profile);
        $this->assertEquals('2.0', $profile->getVersion());
        $this->assertNull($profile->getProcessor());

        $profile = $option($options, null);

        $this->assertInstanceOf('Predis\Profile\ServerProfileInterface', $profile);
        $this->assertInstanceOf(get_class(ServerProfile::getDefault()), $profile);
        $this->assertNull($profile->getProcessor());
    }

    /**
     * @group disconnected
     * @todo Can't we when trap __isset when mocking an interface? Doesn't seem to work here.
     */
    public function testFilterSetsPrefixProcessorFromClientOptions()
    {
        $options = $this->getMock('Predis\Option\ClientOptions', array('__isset', '__get'));
        $options->expects($this->once())
                ->method('__isset')
                ->with('prefix')
                ->will($this->returnValue(true));
        $options->expects($this->once())
                ->method('__get')
                ->with('prefix')
                ->will($this->returnValue(new KeyPrefixProcessor('prefix:')));

        $option = new ClientProfile();

        $profile = $option->filter($options, '2.0');

        $this->assertInstanceOf('Predis\Profile\ServerProfileInterface', $profile);
        $this->assertEquals('2.0', $profile->getVersion());
        $this->assertInstanceOf('Predis\Command\Processor\KeyPrefixProcessor', $profile->getProcessor());
        $this->assertEquals('prefix:', $profile->getProcessor()->getPrefix());
    }

    /**
     * @group disconnected
     * @todo Can't we when trap __isset when mocking an interface? Doesn't seem to work here.
     */
    public function testDefaultSetsPrefixProcessorFromClientOptions()
    {
        $options = $this->getMock('Predis\Option\ClientOptions', array('__isset', '__get'));
        $options->expects($this->once())
                ->method('__isset')
                ->with('prefix')
                ->will($this->returnValue(true));
        $options->expects($this->once())
                ->method('__get')
                ->with('prefix')
                ->will($this->returnValue(new KeyPrefixProcessor('prefix:')));

        $option = new ClientProfile();

        $profile = $option->getDefault($options);

        $this->assertInstanceOf('Predis\Profile\ServerProfileInterface', $profile);
        $this->assertInstanceOf(get_class(ServerProfile::getDefault()), $profile);
        $this->assertInstanceOf('Predis\Command\Processor\KeyPrefixProcessor', $profile->getProcessor());
        $this->assertEquals('prefix:', $profile->getProcessor()->getPrefix());
    }

    /**
     * @group disconnected
     */
    public function testValidationDoesNotSetPrefixProcessorWhenValueIsProfileInstance()
    {
        $options = $this->getMock('Predis\Option\ClientOptions', array('__isset', '__get'));
        $options->expects($this->never())->method('__isset');
        $options->expects($this->never())->method('__get');

        $option = new ClientProfile();

        $profile = $option->filter($options, ServerProfile::getDefault());

        $this->assertInstanceOf('Predis\Profile\ServerProfileInterface', $profile);
        $this->assertNull($profile->getProcessor());
    }

    /**
     * @group disconnected
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid value for the profile option
     */
    public function testValidationThrowsExceptionOnInvalidObjectReturnedByCallback()
    {
        $value = function ($options) {
            return new \stdClass();
        };

        $options = $this->getMock('Predis\Option\ClientOptionsInterface');
        $option = new ClientProfile();

        $option->filter($options, $value);
    }
}
