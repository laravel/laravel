<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\OptionsResolver\Tests;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OptionsResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OptionsResolver
     */
    private $resolver;

    protected function setUp()
    {
        $this->resolver = new OptionsResolver();
    }

    ////////////////////////////////////////////////////////////////////////////
    // resolve()
    ////////////////////////////////////////////////////////////////////////////

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     * @expectedExceptionMessage The option "foo" does not exist. Defined options are: "a", "z".
     */
    public function testResolveFailsIfNonExistingOption()
    {
        $this->resolver->setDefault('z', '1');
        $this->resolver->setDefault('a', '2');

        $this->resolver->resolve(array('foo' => 'bar'));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     * @expectedExceptionMessage The options "baz", "foo", "ping" do not exist. Defined options are: "a", "z".
     */
    public function testResolveFailsIfMultipleNonExistingOptions()
    {
        $this->resolver->setDefault('z', '1');
        $this->resolver->setDefault('a', '2');

        $this->resolver->resolve(array('ping' => 'pong', 'foo' => 'bar', 'baz' => 'bam'));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function testResolveFailsFromLazyOption()
    {
        $this->resolver->setDefault('foo', function (Options $options) {
            $options->resolve(array());
        });

        $this->resolver->resolve();
    }

    ////////////////////////////////////////////////////////////////////////////
    // setDefault()/hasDefault()
    ////////////////////////////////////////////////////////////////////////////

    public function testSetDefaultReturnsThis()
    {
        $this->assertSame($this->resolver, $this->resolver->setDefault('foo', 'bar'));
    }

    public function testSetDefault()
    {
        $this->resolver->setDefault('one', '1');
        $this->resolver->setDefault('two', '20');

        $this->assertEquals(array(
            'one' => '1',
            'two' => '20',
        ), $this->resolver->resolve());
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function testFailIfSetDefaultFromLazyOption()
    {
        $this->resolver->setDefault('lazy', function (Options $options) {
            $options->setDefault('default', 42);
        });

        $this->resolver->resolve();
    }

    public function testHasDefault()
    {
        $this->assertFalse($this->resolver->hasDefault('foo'));
        $this->resolver->setDefault('foo', 42);
        $this->assertTrue($this->resolver->hasDefault('foo'));
    }

    public function testHasDefaultWithNullValue()
    {
        $this->assertFalse($this->resolver->hasDefault('foo'));
        $this->resolver->setDefault('foo', null);
        $this->assertTrue($this->resolver->hasDefault('foo'));
    }

    ////////////////////////////////////////////////////////////////////////////
    // lazy setDefault()
    ////////////////////////////////////////////////////////////////////////////

    public function testSetLazyReturnsThis()
    {
        $this->assertSame($this->resolver, $this->resolver->setDefault('foo', function (Options $options) {}));
    }

    public function testSetLazyClosure()
    {
        $this->resolver->setDefault('foo', function (Options $options) {
            return 'lazy';
        });

        $this->assertEquals(array('foo' => 'lazy'), $this->resolver->resolve());
    }

    public function testClosureWithoutTypeHintNotInvoked()
    {
        $closure = function ($options) {
            \PHPUnit_Framework_Assert::fail('Should not be called');
        };

        $this->resolver->setDefault('foo', $closure);

        $this->assertSame(array('foo' => $closure), $this->resolver->resolve());
    }

    public function testClosureWithoutParametersNotInvoked()
    {
        $closure = function () {
            \PHPUnit_Framework_Assert::fail('Should not be called');
        };

        $this->resolver->setDefault('foo', $closure);

        $this->assertSame(array('foo' => $closure), $this->resolver->resolve());
    }

    public function testAccessPreviousDefaultValue()
    {
        // defined by superclass
        $this->resolver->setDefault('foo', 'bar');

        // defined by subclass
        $this->resolver->setDefault('foo', function (Options $options, $previousValue) {
            \PHPUnit_Framework_Assert::assertEquals('bar', $previousValue);

            return 'lazy';
        });

        $this->assertEquals(array('foo' => 'lazy'), $this->resolver->resolve());
    }

    public function testAccessPreviousLazyDefaultValue()
    {
        // defined by superclass
        $this->resolver->setDefault('foo', function (Options $options) {
            return 'bar';
        });

        // defined by subclass
        $this->resolver->setDefault('foo', function (Options $options, $previousValue) {
            \PHPUnit_Framework_Assert::assertEquals('bar', $previousValue);

            return 'lazy';
        });

        $this->assertEquals(array('foo' => 'lazy'), $this->resolver->resolve());
    }

    public function testPreviousValueIsNotEvaluatedIfNoSecondArgument()
    {
        // defined by superclass
        $this->resolver->setDefault('foo', function () {
            \PHPUnit_Framework_Assert::fail('Should not be called');
        });

        // defined by subclass, no $previousValue argument defined!
        $this->resolver->setDefault('foo', function (Options $options) {
            return 'lazy';
        });

        $this->assertEquals(array('foo' => 'lazy'), $this->resolver->resolve());
    }

    public function testOverwrittenLazyOptionNotEvaluated()
    {
        $this->resolver->setDefault('foo', function (Options $options) {
            \PHPUnit_Framework_Assert::fail('Should not be called');
        });

        $this->resolver->setDefault('foo', 'bar');

        $this->assertSame(array('foo' => 'bar'), $this->resolver->resolve());
    }

    public function testInvokeEachLazyOptionOnlyOnce()
    {
        $calls = 0;

        $this->resolver->setDefault('lazy1', function (Options $options) use (&$calls) {
            \PHPUnit_Framework_Assert::assertSame(1, ++$calls);

            $options['lazy2'];
        });

        $this->resolver->setDefault('lazy2', function (Options $options) use (&$calls) {
            \PHPUnit_Framework_Assert::assertSame(2, ++$calls);
        });

        $this->resolver->resolve();

        $this->assertSame(2, $calls);
    }

    ////////////////////////////////////////////////////////////////////////////
    // setRequired()/isRequired()/getRequiredOptions()
    ////////////////////////////////////////////////////////////////////////////

    public function testSetRequiredReturnsThis()
    {
        $this->assertSame($this->resolver, $this->resolver->setRequired('foo'));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function testFailIfSetRequiredFromLazyOption()
    {
        $this->resolver->setDefault('foo', function (Options $options) {
            $options->setRequired('bar');
        });

        $this->resolver->resolve();
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testResolveFailsIfRequiredOptionMissing()
    {
        $this->resolver->setRequired('foo');

        $this->resolver->resolve();
    }

    public function testResolveSucceedsIfRequiredOptionSet()
    {
        $this->resolver->setRequired('foo');
        $this->resolver->setDefault('foo', 'bar');

        $this->assertNotEmpty($this->resolver->resolve());
    }

    public function testResolveSucceedsIfRequiredOptionPassed()
    {
        $this->resolver->setRequired('foo');

        $this->assertNotEmpty($this->resolver->resolve(array('foo' => 'bar')));
    }

    public function testIsRequired()
    {
        $this->assertFalse($this->resolver->isRequired('foo'));
        $this->resolver->setRequired('foo');
        $this->assertTrue($this->resolver->isRequired('foo'));
    }

    public function testRequiredIfSetBefore()
    {
        $this->assertFalse($this->resolver->isRequired('foo'));

        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->setRequired('foo');

        $this->assertTrue($this->resolver->isRequired('foo'));
    }

    public function testStillRequiredAfterSet()
    {
        $this->assertFalse($this->resolver->isRequired('foo'));

        $this->resolver->setRequired('foo');
        $this->resolver->setDefault('foo', 'bar');

        $this->assertTrue($this->resolver->isRequired('foo'));
    }

    public function testIsNotRequiredAfterRemove()
    {
        $this->assertFalse($this->resolver->isRequired('foo'));
        $this->resolver->setRequired('foo');
        $this->resolver->remove('foo');
        $this->assertFalse($this->resolver->isRequired('foo'));
    }

    public function testIsNotRequiredAfterClear()
    {
        $this->assertFalse($this->resolver->isRequired('foo'));
        $this->resolver->setRequired('foo');
        $this->resolver->clear();
        $this->assertFalse($this->resolver->isRequired('foo'));
    }

    public function testGetRequiredOptions()
    {
        $this->resolver->setRequired(array('foo', 'bar'));
        $this->resolver->setDefault('bam', 'baz');
        $this->resolver->setDefault('foo', 'boo');

        $this->assertSame(array('foo', 'bar'), $this->resolver->getRequiredOptions());
    }

    ////////////////////////////////////////////////////////////////////////////
    // isMissing()/getMissingOptions()
    ////////////////////////////////////////////////////////////////////////////

    public function testIsMissingIfNotSet()
    {
        $this->assertFalse($this->resolver->isMissing('foo'));
        $this->resolver->setRequired('foo');
        $this->assertTrue($this->resolver->isMissing('foo'));
    }

    public function testIsNotMissingIfSet()
    {
        $this->resolver->setDefault('foo', 'bar');

        $this->assertFalse($this->resolver->isMissing('foo'));
        $this->resolver->setRequired('foo');
        $this->assertFalse($this->resolver->isMissing('foo'));
    }

    public function testIsNotMissingAfterRemove()
    {
        $this->resolver->setRequired('foo');
        $this->resolver->remove('foo');
        $this->assertFalse($this->resolver->isMissing('foo'));
    }

    public function testIsNotMissingAfterClear()
    {
        $this->resolver->setRequired('foo');
        $this->resolver->clear();
        $this->assertFalse($this->resolver->isRequired('foo'));
    }

    public function testGetMissingOptions()
    {
        $this->resolver->setRequired(array('foo', 'bar'));
        $this->resolver->setDefault('bam', 'baz');
        $this->resolver->setDefault('foo', 'boo');

        $this->assertSame(array('bar'), $this->resolver->getMissingOptions());
    }

    ////////////////////////////////////////////////////////////////////////////
    // setDefined()/isDefined()/getDefinedOptions()
    ////////////////////////////////////////////////////////////////////////////

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function testFailIfSetDefinedFromLazyOption()
    {
        $this->resolver->setDefault('foo', function (Options $options) {
            $options->setDefined('bar');
        });

        $this->resolver->resolve();
    }

    public function testDefinedOptionsNotIncludedInResolvedOptions()
    {
        $this->resolver->setDefined('foo');

        $this->assertSame(array(), $this->resolver->resolve());
    }

    public function testDefinedOptionsIncludedIfDefaultSetBefore()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->setDefined('foo');

        $this->assertSame(array('foo' => 'bar'), $this->resolver->resolve());
    }

    public function testDefinedOptionsIncludedIfDefaultSetAfter()
    {
        $this->resolver->setDefined('foo');
        $this->resolver->setDefault('foo', 'bar');

        $this->assertSame(array('foo' => 'bar'), $this->resolver->resolve());
    }

    public function testDefinedOptionsIncludedIfPassedToResolve()
    {
        $this->resolver->setDefined('foo');

        $this->assertSame(array('foo' => 'bar'), $this->resolver->resolve(array('foo' => 'bar')));
    }

    public function testIsDefined()
    {
        $this->assertFalse($this->resolver->isDefined('foo'));
        $this->resolver->setDefined('foo');
        $this->assertTrue($this->resolver->isDefined('foo'));
    }

    public function testLazyOptionsAreDefined()
    {
        $this->assertFalse($this->resolver->isDefined('foo'));
        $this->resolver->setDefault('foo', function (Options $options) {});
        $this->assertTrue($this->resolver->isDefined('foo'));
    }

    public function testRequiredOptionsAreDefined()
    {
        $this->assertFalse($this->resolver->isDefined('foo'));
        $this->resolver->setRequired('foo');
        $this->assertTrue($this->resolver->isDefined('foo'));
    }

    public function testSetOptionsAreDefined()
    {
        $this->assertFalse($this->resolver->isDefined('foo'));
        $this->resolver->setDefault('foo', 'bar');
        $this->assertTrue($this->resolver->isDefined('foo'));
    }

    public function testGetDefinedOptions()
    {
        $this->resolver->setDefined(array('foo', 'bar'));
        $this->resolver->setDefault('baz', 'bam');
        $this->resolver->setRequired('boo');

        $this->assertSame(array('foo', 'bar', 'baz', 'boo'), $this->resolver->getDefinedOptions());
    }

    public function testRemovedOptionsAreNotDefined()
    {
        $this->assertFalse($this->resolver->isDefined('foo'));
        $this->resolver->setDefined('foo');
        $this->assertTrue($this->resolver->isDefined('foo'));
        $this->resolver->remove('foo');
        $this->assertFalse($this->resolver->isDefined('foo'));
    }

    public function testClearedOptionsAreNotDefined()
    {
        $this->assertFalse($this->resolver->isDefined('foo'));
        $this->resolver->setDefined('foo');
        $this->assertTrue($this->resolver->isDefined('foo'));
        $this->resolver->clear();
        $this->assertFalse($this->resolver->isDefined('foo'));
    }

    ////////////////////////////////////////////////////////////////////////////
    // setAllowedTypes()
    ////////////////////////////////////////////////////////////////////////////

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     */
    public function testSetAllowedTypesFailsIfUnknownOption()
    {
        $this->resolver->setAllowedTypes('foo', 'string');
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function testFailIfSetAllowedTypesFromLazyOption()
    {
        $this->resolver->setDefault('foo', function (Options $options) {
            $options->setAllowedTypes('bar', 'string');
        });

        $this->resolver->setDefault('bar', 'baz');

        $this->resolver->resolve();
    }

    /**
     * @dataProvider provideInvalidTypes
     */
    public function testResolveFailsIfInvalidType($actualType, $allowedType, $exceptionMessage)
    {
        $this->resolver->setDefined('option');
        $this->resolver->setAllowedTypes('option', $allowedType);
        $this->setExpectedException('Symfony\Component\OptionsResolver\Exception\InvalidOptionsException', $exceptionMessage);
        $this->resolver->resolve(array('option' => $actualType));
    }

    public function provideInvalidTypes()
    {
        return array(
            array(true, 'string', 'The option "option" with value true is expected to be of type "string", but is of type "boolean".'),
            array(false, 'string', 'The option "option" with value false is expected to be of type "string", but is of type "boolean".'),
            array(fopen(__FILE__, 'r'), 'string', 'The option "option" with value resource is expected to be of type "string", but is of type "resource".'),
            array(array(), 'string', 'The option "option" with value array is expected to be of type "string", but is of type "array".'),
            array(new OptionsResolver(), 'string', 'The option "option" with value Symfony\Component\OptionsResolver\OptionsResolver is expected to be of type "string", but is of type "Symfony\Component\OptionsResolver\OptionsResolver".'),
            array(42, 'string', 'The option "option" with value 42 is expected to be of type "string", but is of type "integer".'),
            array(null, 'string', 'The option "option" with value null is expected to be of type "string", but is of type "NULL".'),
            array('bar', '\stdClass', 'The option "option" with value "bar" is expected to be of type "\stdClass", but is of type "string".'),
        );
    }

    public function testResolveSucceedsIfValidType()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->setAllowedTypes('foo', 'string');

        $this->assertNotEmpty($this->resolver->resolve());
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "foo" with value 42 is expected to be of type "string" or "bool", but is of type "integer".
     */
    public function testResolveFailsIfInvalidTypeMultiple()
    {
        $this->resolver->setDefault('foo', 42);
        $this->resolver->setAllowedTypes('foo', array('string', 'bool'));

        $this->resolver->resolve();
    }

    public function testResolveSucceedsIfValidTypeMultiple()
    {
        $this->resolver->setDefault('foo', true);
        $this->resolver->setAllowedTypes('foo', array('string', 'bool'));

        $this->assertNotEmpty($this->resolver->resolve());
    }

    public function testResolveSucceedsIfInstanceOfClass()
    {
        $this->resolver->setDefault('foo', new \stdClass());
        $this->resolver->setAllowedTypes('foo', '\stdClass');

        $this->assertNotEmpty($this->resolver->resolve());
    }

    ////////////////////////////////////////////////////////////////////////////
    // addAllowedTypes()
    ////////////////////////////////////////////////////////////////////////////

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     */
    public function testAddAllowedTypesFailsIfUnknownOption()
    {
        $this->resolver->addAllowedTypes('foo', 'string');
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function testFailIfAddAllowedTypesFromLazyOption()
    {
        $this->resolver->setDefault('foo', function (Options $options) {
            $options->addAllowedTypes('bar', 'string');
        });

        $this->resolver->setDefault('bar', 'baz');

        $this->resolver->resolve();
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testResolveFailsIfInvalidAddedType()
    {
        $this->resolver->setDefault('foo', 42);
        $this->resolver->addAllowedTypes('foo', 'string');

        $this->resolver->resolve();
    }

    public function testResolveSucceedsIfValidAddedType()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->addAllowedTypes('foo', 'string');

        $this->assertNotEmpty($this->resolver->resolve());
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testResolveFailsIfInvalidAddedTypeMultiple()
    {
        $this->resolver->setDefault('foo', 42);
        $this->resolver->addAllowedTypes('foo', array('string', 'bool'));

        $this->resolver->resolve();
    }

    public function testResolveSucceedsIfValidAddedTypeMultiple()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->addAllowedTypes('foo', array('string', 'bool'));

        $this->assertNotEmpty($this->resolver->resolve());
    }

    public function testAddAllowedTypesDoesNotOverwrite()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->setAllowedTypes('foo', 'string');
        $this->resolver->addAllowedTypes('foo', 'bool');

        $this->resolver->setDefault('foo', 'bar');

        $this->assertNotEmpty($this->resolver->resolve());
    }

    public function testAddAllowedTypesDoesNotOverwrite2()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->setAllowedTypes('foo', 'string');
        $this->resolver->addAllowedTypes('foo', 'bool');

        $this->resolver->setDefault('foo', false);

        $this->assertNotEmpty($this->resolver->resolve());
    }

    ////////////////////////////////////////////////////////////////////////////
    // setAllowedValues()
    ////////////////////////////////////////////////////////////////////////////

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     */
    public function testSetAllowedValuesFailsIfUnknownOption()
    {
        $this->resolver->setAllowedValues('foo', 'bar');
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function testFailIfSetAllowedValuesFromLazyOption()
    {
        $this->resolver->setDefault('foo', function (Options $options) {
            $options->setAllowedValues('bar', 'baz');
        });

        $this->resolver->setDefault('bar', 'baz');

        $this->resolver->resolve();
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "foo" with value 42 is invalid. Accepted values are: "bar".
     */
    public function testResolveFailsIfInvalidValue()
    {
        $this->resolver->setDefined('foo');
        $this->resolver->setAllowedValues('foo', 'bar');

        $this->resolver->resolve(array('foo' => 42));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "foo" with value null is invalid. Accepted values are: "bar".
     */
    public function testResolveFailsIfInvalidValueIsNull()
    {
        $this->resolver->setDefault('foo', null);
        $this->resolver->setAllowedValues('foo', 'bar');

        $this->resolver->resolve();
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testResolveFailsIfInvalidValueStrict()
    {
        $this->resolver->setDefault('foo', 42);
        $this->resolver->setAllowedValues('foo', '42');

        $this->resolver->resolve();
    }

    public function testResolveSucceedsIfValidValue()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->setAllowedValues('foo', 'bar');

        $this->assertEquals(array('foo' => 'bar'), $this->resolver->resolve());
    }

    public function testResolveSucceedsIfValidValueIsNull()
    {
        $this->resolver->setDefault('foo', null);
        $this->resolver->setAllowedValues('foo', null);

        $this->assertEquals(array('foo' => null), $this->resolver->resolve());
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     * @expectedExceptionMessage The option "foo" with value 42 is invalid. Accepted values are: "bar", false, null.
     */
    public function testResolveFailsIfInvalidValueMultiple()
    {
        $this->resolver->setDefault('foo', 42);
        $this->resolver->setAllowedValues('foo', array('bar', false, null));

        $this->resolver->resolve();
    }

    public function testResolveSucceedsIfValidValueMultiple()
    {
        $this->resolver->setDefault('foo', 'baz');
        $this->resolver->setAllowedValues('foo', array('bar', 'baz'));

        $this->assertEquals(array('foo' => 'baz'), $this->resolver->resolve());
    }

    public function testResolveFailsIfClosureReturnsFalse()
    {
        $this->resolver->setDefault('foo', 42);
        $this->resolver->setAllowedValues('foo', function ($value) use (&$passedValue) {
            $passedValue = $value;

            return false;
        });

        try {
            $this->resolver->resolve();
            $this->fail('Should fail');
        } catch (InvalidOptionsException $e) {
        }

        $this->assertSame(42, $passedValue);
    }

    public function testResolveSucceedsIfClosureReturnsTrue()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->setAllowedValues('foo', function ($value) use (&$passedValue) {
            $passedValue = $value;

            return true;
        });

        $this->assertEquals(array('foo' => 'bar'), $this->resolver->resolve());
        $this->assertSame('bar', $passedValue);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testResolveFailsIfAllClosuresReturnFalse()
    {
        $this->resolver->setDefault('foo', 42);
        $this->resolver->setAllowedValues('foo', array(
            function () { return false; },
            function () { return false; },
            function () { return false; },
        ));

        $this->resolver->resolve();
    }

    public function testResolveSucceedsIfAnyClosureReturnsTrue()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->setAllowedValues('foo', array(
            function () { return false; },
            function () { return true; },
            function () { return false; },
        ));

        $this->assertEquals(array('foo' => 'bar'), $this->resolver->resolve());
    }

    ////////////////////////////////////////////////////////////////////////////
    // addAllowedValues()
    ////////////////////////////////////////////////////////////////////////////

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     */
    public function testAddAllowedValuesFailsIfUnknownOption()
    {
        $this->resolver->addAllowedValues('foo', 'bar');
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function testFailIfAddAllowedValuesFromLazyOption()
    {
        $this->resolver->setDefault('foo', function (Options $options) {
            $options->addAllowedValues('bar', 'baz');
        });

        $this->resolver->setDefault('bar', 'baz');

        $this->resolver->resolve();
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testResolveFailsIfInvalidAddedValue()
    {
        $this->resolver->setDefault('foo', 42);
        $this->resolver->addAllowedValues('foo', 'bar');

        $this->resolver->resolve();
    }

    public function testResolveSucceedsIfValidAddedValue()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->addAllowedValues('foo', 'bar');

        $this->assertEquals(array('foo' => 'bar'), $this->resolver->resolve());
    }

    public function testResolveSucceedsIfValidAddedValueIsNull()
    {
        $this->resolver->setDefault('foo', null);
        $this->resolver->addAllowedValues('foo', null);

        $this->assertEquals(array('foo' => null), $this->resolver->resolve());
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testResolveFailsIfInvalidAddedValueMultiple()
    {
        $this->resolver->setDefault('foo', 42);
        $this->resolver->addAllowedValues('foo', array('bar', 'baz'));

        $this->resolver->resolve();
    }

    public function testResolveSucceedsIfValidAddedValueMultiple()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->addAllowedValues('foo', array('bar', 'baz'));

        $this->assertEquals(array('foo' => 'bar'), $this->resolver->resolve());
    }

    public function testAddAllowedValuesDoesNotOverwrite()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->setAllowedValues('foo', 'bar');
        $this->resolver->addAllowedValues('foo', 'baz');

        $this->assertEquals(array('foo' => 'bar'), $this->resolver->resolve());
    }

    public function testAddAllowedValuesDoesNotOverwrite2()
    {
        $this->resolver->setDefault('foo', 'baz');
        $this->resolver->setAllowedValues('foo', 'bar');
        $this->resolver->addAllowedValues('foo', 'baz');

        $this->assertEquals(array('foo' => 'baz'), $this->resolver->resolve());
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testResolveFailsIfAllAddedClosuresReturnFalse()
    {
        $this->resolver->setDefault('foo', 42);
        $this->resolver->setAllowedValues('foo', function () { return false; });
        $this->resolver->addAllowedValues('foo', function () { return false; });

        $this->resolver->resolve();
    }

    public function testResolveSucceedsIfAnyAddedClosureReturnsTrue()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->setAllowedValues('foo', function () { return false; });
        $this->resolver->addAllowedValues('foo', function () { return true; });

        $this->assertEquals(array('foo' => 'bar'), $this->resolver->resolve());
    }

    public function testResolveSucceedsIfAnyAddedClosureReturnsTrue2()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->setAllowedValues('foo', function () { return true; });
        $this->resolver->addAllowedValues('foo', function () { return false; });

        $this->assertEquals(array('foo' => 'bar'), $this->resolver->resolve());
    }

    ////////////////////////////////////////////////////////////////////////////
    // setNormalizer()
    ////////////////////////////////////////////////////////////////////////////

    public function testSetNormalizerReturnsThis()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->assertSame($this->resolver, $this->resolver->setNormalizer('foo', function () {}));
    }

    public function testSetNormalizerClosure()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->setNormalizer('foo', function () {
            return 'normalized';
        });

        $this->assertEquals(array('foo' => 'normalized'), $this->resolver->resolve());
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     */
    public function testSetNormalizerFailsIfUnknownOption()
    {
        $this->resolver->setNormalizer('foo', function () {});
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function testFailIfSetNormalizerFromLazyOption()
    {
        $this->resolver->setDefault('foo', function (Options $options) {
            $options->setNormalizer('foo', function () {});
        });

        $this->resolver->setDefault('bar', 'baz');

        $this->resolver->resolve();
    }

    public function testNormalizerReceivesSetOption()
    {
        $this->resolver->setDefault('foo', 'bar');

        $this->resolver->setNormalizer('foo', function (Options $options, $value) {
            return 'normalized['.$value.']';
        });

        $this->assertEquals(array('foo' => 'normalized[bar]'), $this->resolver->resolve());
    }

    public function testNormalizerReceivesPassedOption()
    {
        $this->resolver->setDefault('foo', 'bar');

        $this->resolver->setNormalizer('foo', function (Options $options, $value) {
            return 'normalized['.$value.']';
        });

        $resolved = $this->resolver->resolve(array('foo' => 'baz'));

        $this->assertEquals(array('foo' => 'normalized[baz]'), $resolved);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testValidateTypeBeforeNormalization()
    {
        $this->resolver->setDefault('foo', 'bar');

        $this->resolver->setAllowedTypes('foo', 'int');

        $this->resolver->setNormalizer('foo', function () {
            \PHPUnit_Framework_Assert::fail('Should not be called.');
        });

        $this->resolver->resolve();
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testValidateValueBeforeNormalization()
    {
        $this->resolver->setDefault('foo', 'bar');

        $this->resolver->setAllowedValues('foo', 'baz');

        $this->resolver->setNormalizer('foo', function () {
            \PHPUnit_Framework_Assert::fail('Should not be called.');
        });

        $this->resolver->resolve();
    }

    public function testNormalizerCanAccessOtherOptions()
    {
        $this->resolver->setDefault('default', 'bar');
        $this->resolver->setDefault('norm', 'baz');

        $this->resolver->setNormalizer('norm', function (Options $options) {
            /* @var \PHPUnit_Framework_TestCase $test */
            \PHPUnit_Framework_Assert::assertSame('bar', $options['default']);

            return 'normalized';
        });

        $this->assertEquals(array(
            'default' => 'bar',
            'norm' => 'normalized',
        ), $this->resolver->resolve());
    }

    public function testNormalizerCanAccessLazyOptions()
    {
        $this->resolver->setDefault('lazy', function (Options $options) {
            return 'bar';
        });
        $this->resolver->setDefault('norm', 'baz');

        $this->resolver->setNormalizer('norm', function (Options $options) {
            /* @var \PHPUnit_Framework_TestCase $test */
            \PHPUnit_Framework_Assert::assertEquals('bar', $options['lazy']);

            return 'normalized';
        });

        $this->assertEquals(array(
            'lazy' => 'bar',
            'norm' => 'normalized',
        ), $this->resolver->resolve());
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\OptionDefinitionException
     */
    public function testFailIfCyclicDependencyBetweenNormalizers()
    {
        $this->resolver->setDefault('norm1', 'bar');
        $this->resolver->setDefault('norm2', 'baz');

        $this->resolver->setNormalizer('norm1', function (Options $options) {
            $options['norm2'];
        });

        $this->resolver->setNormalizer('norm2', function (Options $options) {
            $options['norm1'];
        });

        $this->resolver->resolve();
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\OptionDefinitionException
     */
    public function testFailIfCyclicDependencyBetweenNormalizerAndLazyOption()
    {
        $this->resolver->setDefault('lazy', function (Options $options) {
            $options['norm'];
        });

        $this->resolver->setDefault('norm', 'baz');

        $this->resolver->setNormalizer('norm', function (Options $options) {
            $options['lazy'];
        });

        $this->resolver->resolve();
    }

    public function testCatchedExceptionFromNormalizerDoesNotCrashOptionResolver()
    {
        $throw = true;

        $this->resolver->setDefaults(array('catcher' => null, 'thrower' => null));

        $this->resolver->setNormalizer('catcher', function (Options $options) {
            try {
                return $options['thrower'];
            } catch (\Exception $e) {
                return false;
            }
        });

        $this->resolver->setNormalizer('thrower', function (Options $options) use (&$throw) {
            if ($throw) {
                $throw = false;
                throw new \UnexpectedValueException('throwing');
            }

            return true;
        });

        $this->resolver->resolve();
    }

    public function testCatchedExceptionFromLazyDoesNotCrashOptionResolver()
    {
        $throw = true;

        $this->resolver->setDefault('catcher', function (Options $options) {
            try {
                return $options['thrower'];
            } catch (\Exception $e) {
                return false;
            }
        });

        $this->resolver->setDefault('thrower', function (Options $options) use (&$throw) {
            if ($throw) {
                $throw = false;
                throw new \UnexpectedValueException('throwing');
            }

            return true;
        });

        $this->resolver->resolve();
    }

    public function testInvokeEachNormalizerOnlyOnce()
    {
        $calls = 0;

        $this->resolver->setDefault('norm1', 'bar');
        $this->resolver->setDefault('norm2', 'baz');

        $this->resolver->setNormalizer('norm1', function ($options) use (&$calls) {
            \PHPUnit_Framework_Assert::assertSame(1, ++$calls);

            $options['norm2'];
        });
        $this->resolver->setNormalizer('norm2', function () use (&$calls) {
            \PHPUnit_Framework_Assert::assertSame(2, ++$calls);
        });

        $this->resolver->resolve();

        $this->assertSame(2, $calls);
    }

    public function testNormalizerNotCalledForUnsetOptions()
    {
        $this->resolver->setDefined('norm');

        $this->resolver->setNormalizer('norm', function () {
            \PHPUnit_Framework_Assert::fail('Should not be called.');
        });

        $this->assertEmpty($this->resolver->resolve());
    }

    ////////////////////////////////////////////////////////////////////////////
    // setDefaults()
    ////////////////////////////////////////////////////////////////////////////

    public function testSetDefaultsReturnsThis()
    {
        $this->assertSame($this->resolver, $this->resolver->setDefaults(array('foo', 'bar')));
    }

    public function testSetDefaults()
    {
        $this->resolver->setDefault('one', '1');
        $this->resolver->setDefault('two', 'bar');

        $this->resolver->setDefaults(array(
            'two' => '2',
            'three' => '3',
        ));

        $this->assertEquals(array(
            'one' => '1',
            'two' => '2',
            'three' => '3',
        ), $this->resolver->resolve());
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function testFailIfSetDefaultsFromLazyOption()
    {
        $this->resolver->setDefault('foo', function (Options $options) {
            $options->setDefaults(array('two' => '2'));
        });

        $this->resolver->resolve();
    }

    ////////////////////////////////////////////////////////////////////////////
    // remove()
    ////////////////////////////////////////////////////////////////////////////

    public function testRemoveReturnsThis()
    {
        $this->resolver->setDefault('foo', 'bar');

        $this->assertSame($this->resolver, $this->resolver->remove('foo'));
    }

    public function testRemoveSingleOption()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->setDefault('baz', 'boo');
        $this->resolver->remove('foo');

        $this->assertSame(array('baz' => 'boo'), $this->resolver->resolve());
    }

    public function testRemoveMultipleOptions()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->setDefault('baz', 'boo');
        $this->resolver->setDefault('doo', 'dam');

        $this->resolver->remove(array('foo', 'doo'));

        $this->assertSame(array('baz' => 'boo'), $this->resolver->resolve());
    }

    public function testRemoveLazyOption()
    {
        $this->resolver->setDefault('foo', function (Options $options) {
            return 'lazy';
        });
        $this->resolver->remove('foo');

        $this->assertSame(array(), $this->resolver->resolve());
    }

    public function testRemoveNormalizer()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->setNormalizer('foo', function (Options $options, $value) {
            return 'normalized';
        });
        $this->resolver->remove('foo');
        $this->resolver->setDefault('foo', 'bar');

        $this->assertSame(array('foo' => 'bar'), $this->resolver->resolve());
    }

    public function testRemoveAllowedTypes()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->setAllowedTypes('foo', 'int');
        $this->resolver->remove('foo');
        $this->resolver->setDefault('foo', 'bar');

        $this->assertSame(array('foo' => 'bar'), $this->resolver->resolve());
    }

    public function testRemoveAllowedValues()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->setAllowedValues('foo', array('baz', 'boo'));
        $this->resolver->remove('foo');
        $this->resolver->setDefault('foo', 'bar');

        $this->assertSame(array('foo' => 'bar'), $this->resolver->resolve());
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function testFailIfRemoveFromLazyOption()
    {
        $this->resolver->setDefault('foo', function (Options $options) {
            $options->remove('bar');
        });

        $this->resolver->setDefault('bar', 'baz');

        $this->resolver->resolve();
    }

    public function testRemoveUnknownOptionIgnored()
    {
        $this->assertNotNull($this->resolver->remove('foo'));
    }

    ////////////////////////////////////////////////////////////////////////////
    // clear()
    ////////////////////////////////////////////////////////////////////////////

    public function testClearReturnsThis()
    {
        $this->assertSame($this->resolver, $this->resolver->clear());
    }

    public function testClearRemovesAllOptions()
    {
        $this->resolver->setDefault('one', 1);
        $this->resolver->setDefault('two', 2);

        $this->resolver->clear();

        $this->assertEmpty($this->resolver->resolve());
    }

    public function testClearLazyOption()
    {
        $this->resolver->setDefault('foo', function (Options $options) {
            return 'lazy';
        });
        $this->resolver->clear();

        $this->assertSame(array(), $this->resolver->resolve());
    }

    public function testClearNormalizer()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->setNormalizer('foo', function (Options $options, $value) {
            return 'normalized';
        });
        $this->resolver->clear();
        $this->resolver->setDefault('foo', 'bar');

        $this->assertSame(array('foo' => 'bar'), $this->resolver->resolve());
    }

    public function testClearAllowedTypes()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->setAllowedTypes('foo', 'int');
        $this->resolver->clear();
        $this->resolver->setDefault('foo', 'bar');

        $this->assertSame(array('foo' => 'bar'), $this->resolver->resolve());
    }

    public function testClearAllowedValues()
    {
        $this->resolver->setDefault('foo', 'bar');
        $this->resolver->setAllowedValues('foo', 'baz');
        $this->resolver->clear();
        $this->resolver->setDefault('foo', 'bar');

        $this->assertSame(array('foo' => 'bar'), $this->resolver->resolve());
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function testFailIfClearFromLazyption()
    {
        $this->resolver->setDefault('foo', function (Options $options) {
            $options->clear();
        });

        $this->resolver->setDefault('bar', 'baz');

        $this->resolver->resolve();
    }

    public function testClearOptionAndNormalizer()
    {
        $this->resolver->setDefault('foo1', 'bar');
        $this->resolver->setNormalizer('foo1', function (Options $options) {
            return '';
        });
        $this->resolver->setDefault('foo2', 'bar');
        $this->resolver->setNormalizer('foo2', function (Options $options) {
            return '';
        });

        $this->resolver->clear();
        $this->assertEmpty($this->resolver->resolve());
    }

    ////////////////////////////////////////////////////////////////////////////
    // ArrayAccess
    ////////////////////////////////////////////////////////////////////////////

    public function testArrayAccess()
    {
        $this->resolver->setDefault('default1', 0);
        $this->resolver->setDefault('default2', 1);
        $this->resolver->setRequired('required');
        $this->resolver->setDefined('defined');
        $this->resolver->setDefault('lazy1', function (Options $options) {
            return 'lazy';
        });

        $this->resolver->setDefault('lazy2', function (Options $options) {
            \PHPUnit_Framework_Assert::assertTrue(isset($options['default1']));
            \PHPUnit_Framework_Assert::assertTrue(isset($options['default2']));
            \PHPUnit_Framework_Assert::assertTrue(isset($options['required']));
            \PHPUnit_Framework_Assert::assertTrue(isset($options['lazy1']));
            \PHPUnit_Framework_Assert::assertTrue(isset($options['lazy2']));
            \PHPUnit_Framework_Assert::assertFalse(isset($options['defined']));

            \PHPUnit_Framework_Assert::assertSame(0, $options['default1']);
            \PHPUnit_Framework_Assert::assertSame(42, $options['default2']);
            \PHPUnit_Framework_Assert::assertSame('value', $options['required']);
            \PHPUnit_Framework_Assert::assertSame('lazy', $options['lazy1']);

            // Obviously $options['lazy'] and $options['defined'] cannot be
            // accessed
        });

        $this->resolver->resolve(array('default2' => 42, 'required' => 'value'));
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function testArrayAccessGetFailsOutsideResolve()
    {
        $this->resolver->setDefault('default', 0);

        $this->resolver['default'];
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function testArrayAccessExistsFailsOutsideResolve()
    {
        $this->resolver->setDefault('default', 0);

        isset($this->resolver['default']);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function testArrayAccessSetNotSupported()
    {
        $this->resolver['default'] = 0;
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function testArrayAccessUnsetNotSupported()
    {
        $this->resolver->setDefault('default', 0);

        unset($this->resolver['default']);
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException
     * @expectedExceptionMessage The option "undefined" does not exist. Defined options are: "foo", "lazy".
     */
    public function testFailIfGetNonExisting()
    {
        $this->resolver->setDefault('foo', 'bar');

        $this->resolver->setDefault('lazy', function (Options $options) {
            $options['undefined'];
        });

        $this->resolver->resolve();
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\NoSuchOptionException
     * @expectedExceptionMessage The optional option "defined" has no value set. You should make sure it is set with "isset" before reading it.
     */
    public function testFailIfGetDefinedButUnset()
    {
        $this->resolver->setDefined('defined');

        $this->resolver->setDefault('lazy', function (Options $options) {
            $options['defined'];
        });

        $this->resolver->resolve();
    }

    /**
     * @expectedException \Symfony\Component\OptionsResolver\Exception\OptionDefinitionException
     */
    public function testFailIfCyclicDependency()
    {
        $this->resolver->setDefault('lazy1', function (Options $options) {
            $options['lazy2'];
        });

        $this->resolver->setDefault('lazy2', function (Options $options) {
            $options['lazy1'];
        });

        $this->resolver->resolve();
    }

    ////////////////////////////////////////////////////////////////////////////
    // Countable
    ////////////////////////////////////////////////////////////////////////////

    public function testCount()
    {
        $this->resolver->setDefault('default', 0);
        $this->resolver->setRequired('required');
        $this->resolver->setDefined('defined');
        $this->resolver->setDefault('lazy1', function () {});

        $this->resolver->setDefault('lazy2', function (Options $options) {
            \PHPUnit_Framework_Assert::assertCount(4, $options);
        });

        $this->assertCount(4, $this->resolver->resolve(array('required' => 'value')));
    }

    /**
     * In resolve() we count the options that are actually set (which may be
     * only a subset of the defined options). Outside of resolve(), it's not
     * clear what is counted.
     *
     * @expectedException \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    public function testCountFailsOutsideResolve()
    {
        $this->resolver->setDefault('foo', 0);
        $this->resolver->setRequired('bar');
        $this->resolver->setDefined('bar');
        $this->resolver->setDefault('lazy1', function () {});

        count($this->resolver);
    }
}
